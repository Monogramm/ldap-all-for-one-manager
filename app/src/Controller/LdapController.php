<?php

namespace App\Controller;

use App\Service\Ldap\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\DTO\LdapEntryDTO;
use PayPalHttp\Serializer;
use Symfony\Component\ErrorHandler\ErrorRenderer\SerializerErrorRenderer;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class LdapController extends AbstractController
{
    /**
     * @Route("/api/ldap", name="get_ldap_entries", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getLdapEntries(
        Client $ldap,
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $query = $request->get('query', '(objectClass=*)');
        $baseDn = $request->get('base', null);

        $options = [];

        $filters = json_decode($request->get('filters', '[]'), true);
        if (!empty($filters)) {
            $queryFilters = '';
            foreach ($filters as $field => $value) {
                switch ($field) {
                    case 'dn':
                        $queryFilters .= "($value)";
                        break;

                    default:
                        $queryFilters .= "($field=$value)";
                        break;
                }
            }
            $query = "(&$queryFilters$query)";
        }
        // XXX Is there any way to ask LDAP server to sort results?
        //$orders  = json_decode($request->get('orders', null), true);

        // Attributes option.
        $attributes = $request->get('attributes', ['dn']);
        $options['filter'] = $attributes;

        // Add query options.
        $size = (int) $request->get('size', 0);
        $max = (int) $request->get('max', 0);

        $options['pageSize'] = $size;
        $options['maxItems'] = $max;

        $ldap->bind();
        $ldapEntries = $ldap->search($query, $baseDn, $options);

        $total = count($ldapEntries);

        $entries = array();
        foreach ($ldapEntries as $key => $ldapEntry) {
            $entries[$key]['dn'] = $ldapEntry->getDn();
            // TODO Create a LdapEntry DTO for serialization from/to Entry (in particular jpegPhoto)
            $ldapDto = new LdapEntryDTO();
            $ldapEntry =  $ldapDto->serializeJpegPhoto($ldapEntry);

            // Rely on filter option to filter attributes
            $entries[$key]['attributes'] = $ldapEntry->getAttributes();
        }

        $entries = $serializer->normalize(
            $entries,
            Entry::class
        );

        return new JsonResponse([
            'total' => $total,
            'items' => $entries,
        ]);
    }

    /**
     * @Route("/api/ldap/{entry}", name="get_ldap_entry", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getLdapEntryByDn(
        string $entry,
        Client $ldap,
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $jsonResponse = new JsonResponse();

        $query = $request->get('query', '(objectClass=*)');
        // TODO Add attributes option.
        $options = $request->get('options', []);

        if (empty($entry)) {
            // TODO Return translated error message.
            return $jsonResponse->create(null, 400);
        }

        $ldap->bind();
        $ldapEntry = $ldap->get($query, $entry, $options);
        if (empty($ldapEntry)) {
            // TODO Return translated error message.
            return $jsonResponse->create(null, 400);
        }

        // TODO Create a LdapEntry DTO for serialization from/to Entry (in particular jpegPhoto)
        $ldapDto = new LdapEntryDTO();
        $ldapEntry =  $ldapDto->serializeJpegPhoto($ldapEntry);

        $dto = $serializer->serialize($ldapEntry, 'json');

        return $jsonResponse->fromJsonString($dto);
    }

    /**
     * @Route("/api/admin/ldap", name="create_ldap_entry", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function createLdapEntry(
        Client $ldap,
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $jsonResponse = new JsonResponse();

        try {
            $dto = $serializer->deserialize(
                $request->getContent(),
                Entry::class,
                'json'
            );
        } catch (NotEncodableValueException $exception) {
            return $jsonResponse->create(null, 400);
        }

        // TODO Deserialize from base64 jpegPhoto.
        try {
            // TODO Check result.
            $ldap->bind();
            $result = $ldap->create($dto->getDn(), $dto->getAttributes());
            
            return $jsonResponse->fromJsonString(
                $serializer->serialize($result, 'json')
            );
        } catch (LdapException $exception) {
            // TODO Log the exception.
            return $jsonResponse->create($exception->getMessage(), 500);
        }
    }

    /**
     * @Route("/api/admin/ldap/{fullDN}", name="edit_ldap_entry", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function editLdapEntry(
        string $fullDN,
        Client $ldap,
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $query = $request->get('query', '(objectClass=*)');
        /**
         * @var Entry $dto
         */

        $jsonResponse = new JsonResponse();

        try {
            $dto = $serializer->deserialize(
                $request->getContent(),
                Entry::class,
                'json'
            );
        } catch (NotEncodableValueException $exception) {
            return $jsonResponse->create(null, 400);
        }

        // TODO Deserialize from base64 jpegPhoto.

        try {
            $ldap->bind();

            // TODO Check result.
            if ($result = $ldap->update($fullDN, $query, $dto->getAttributes()) === true) {
                return $jsonResponse->fromJsonString(
                    $serializer->serialize($result, 'json')
                );
            };

            //TODO MODIFY DEFAULT RETURN
            return $jsonResponse->create($result, 500);
        } catch (LdapException $exception) {
            // TODO Log the exception.
            return $jsonResponse->create($exception->getMessage(), 500);
        }
    }

    /**
     * @Route("/api/admin/ldap/{fullDn}", name="delete_ldap_entry", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteLdapEntry(
        string $fullDn,
        Client $ldap
    ): JsonResponse {
        $result = false;
        $status = 400;
        $jsonResponse = new JsonResponse();

        if (empty($fullDn)) {
            // TODO Return translated error message.
            return $jsonResponse->create(null, 400);
        }

        try {
            $ldap->bind();
            $result = $ldap->delete($fullDn);
            $status = 200;
        } catch (LdapException $exception) {
            // TODO Log the exception.
            $result = $exception->getMessage();
            $status = 500;
        }

        return $jsonResponse->create($result, $status);
    }
}
