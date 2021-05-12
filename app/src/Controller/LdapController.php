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

        $ldapEntries = $ldap->search($query, $baseDn, $options);

        $total = count($ldapEntries);

        // TODO Create a LdapEntry DTO for serialization from/to Entry (in particular jpegPhoto)
        $entries = array();
        foreach ($ldapEntries as $key => $ldapEntry) {
            $entries[$key]['dn'] = $ldapEntry->getDn();

            // TODO Serialize in base64 jpegPhoto.
            if (!empty($ldapEntry->hasAttribute('jpegPhoto')) && !empty($ldapEntry->getAttribute('jpegPhoto'))) {
                // Serialize in base64 jpegPhoto.
                $jpegPhotos = array();
                foreach ($ldapEntry->getAttribute('jpegPhoto') as $jpegPhoto) {
                    $jpegPhotos[] = base64_encode($jpegPhoto);
                }
                $ldapEntry->setAttribute('jpegPhoto', $jpegPhotos);
            }
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
        $query = $request->get('query', '(objectClass=*)');
        // TODO Add attributes option.

        $ldapEntry = $ldap->get($query, $entry);
        if (empty($ldapEntry)) {
            // TODO Return translated error message.
            return JsonResponse::create(null, 404);
        }

        // TODO Create a LdapEntry DTO for serialization from/to Entry (in particular jpegPhoto)
        if (!empty($ldapEntry->hasAttribute('jpegPhoto')) && !empty($ldapEntry->getAttribute('jpegPhoto'))) {
            // Serialize in base64 jpegPhoto.
            $jpegPhotos = array();
            foreach ($ldapEntry->getAttribute('jpegPhoto') as $jpegPhoto) {
                $jpegPhotos[] = base64_encode($jpegPhoto);
            }
            $ldapEntry->setAttribute('jpegPhoto', $jpegPhotos);
        }

        $dto = $serializer->serialize($ldapEntry, 'json');

        return JsonResponse::fromJsonString($dto);
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
        $dto = $serializer->deserialize(
            $request->getContent(),
            Entry::class,
            'json'
        );

        // TODO Deserialize from base64 jpegPhoto.

        try {
            // TODO Check result.
            $result = $ldap->create($dto->getDn(), $dto->getAttributes());

            return JsonResponse::fromJsonString(
                $serializer->serialize($dto, 'json')
            );
        } catch (LdapException $exception) {
            // TODO Log the exception.
            return JsonResponse::create($exception->getMessage(), 500);
        }
    }

    /**
     * @Route("/api/admin/ldap/{entry}", name="edit_ldap_entry", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function editLdapEntryByQuery(
        string $entry,
        Client $ldap,
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $query = $request->get('query', '(objectClass=*)');

        /**
         * @var Ldap $dto
         */
        $dto = $serializer->deserialize(
            $request->getContent(),
            Entry::class,
            'json'
        );
        // TODO Deserialize from base64 jpegPhoto.

        try {
            // TODO Check result.
            $result = $ldap->update($query, $entry, $dto->getAttributes());

            return JsonResponse::fromJsonString(
                $serializer->serialize($dto, 'json')
            );
        } catch (LdapException $exception) {
            // TODO Log the exception.
            return JsonResponse::create($exception->getMessage(), 500);
        }
    }

    /**
     * @Route("/api/admin/ldap/{entry}", name="delete_ldap_entry", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteLdapEntry(
        string $entry,
        Client $ldap
    ): JsonResponse {
        $result = false;
        $status = 400;
        try {
            $result = $ldap->delete($entry);
            $status = 200;
        } catch (LdapException $exception) {
            // TODO Log the exception.
            $result = $exception->getMessage();
            $status = 500;
        }

        return JsonResponse::create($result, $status);
    }
}
