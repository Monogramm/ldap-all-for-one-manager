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
        $query = $request->get('query', '(objectClass=inetOrgPerson)');
        // Attributes option.
        $attributes = $request->get('attributes', []);

        // TODO Add query options.
        //$page = (int) $request->get('page', 1);
        //$size = (int) $request->get('size', 20);
        //$scope = (int) $request->get('scope', QueryInterface::SCOPE_SUB);
        // TODO Add order option.
        //$orders  = json_decode($request->get('orders', null), true);
        // TODO Serialize in base64 jpegPhoto.

        $ldapEntries = $ldap->search($query);

        $total = count($ldapEntries);

        // TODO Create a LdapEntry DTO for serialization from/to Entry
        $entries = array();
        foreach ($ldapEntries as $key => $entry) {
            $entries[$key]['dn'] = $entry->getDn();

            $entryAttributes = array();
            foreach ($attributes as $attribute) {
                $entryAttributes[$attribute] = ($entry->hasAttribute($attribute) && !empty($entry->getAttribute($attribute))) ?
                    json_encode($entry->getAttribute($attribute)) : null;
            }
            $entries[$key]['attributes'] = $entryAttributes;
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
     * @Route("/api/ldap/{query}", name="get_ldap_entry", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getLdapEntryByDn(
        string $query,
        Client $ldap,
        SerializerInterface $serializer
    ): JsonResponse {
        // TODO Add attributes option.

        $entries = $ldap->search("($query)");

        $entry = null;
        if (empty($entries) || ! is_array($entries)) {
            // TODO Return translated error message.
            return JsonResponse::create(null, 404);
        } else if ( 1 !== count($entries) ) {
            // Bad request: too many results.
            // TODO Return translated error message.
            return JsonResponse::create(null, 400);
        }
        $entry = $entries[0];

        $dto = $serializer->serialize($entry, 'json');
        // TODO Serialize in base64 jpegPhoto.

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
     * @Route("/api/admin/ldap/{query}", name="edit_ldap_entry", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function editLdapEntryByQuery(
        string $query,
        Client $ldap,
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
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
            $result = $ldap->update("($query)", $dto->getAttributes());

            return JsonResponse::fromJsonString(
                $serializer->serialize($dto, 'json')
            );
        } catch (LdapException $exception) {
            // TODO Log the exception.
            return JsonResponse::create($exception->getMessage(), 500);
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
        try {
            $result = $ldap->delete($fullDn);
            $status = 200;
        } catch (LdapException $exception) {
            // TODO Log the exception.
            $result = $exception->getMessage();
            $status = 500;
        }

        return JsonResponse::create($result, $status);
    }
}
