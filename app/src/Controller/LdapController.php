<?php

namespace App\Controller;

use App\Service\Ldap\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Entry;
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
        $query = json_decode($request->get('query', '()'), true);
        // TODO Add order option.
        //$orders  = json_decode($request->get('orders', null), true);
        // TODO Serialize in base64 jpegPhoto.
        // TODO Add attributes option.

        $ldapEntries = $ldap->search($query);

        $total = count($ldapEntries);
        $entries = $serializer->serialize($ldapEntries, 'json');

        return new JsonResponse([
            'total' => $total,
            'items' => $entries,
        ]);
    }

    /**
     * @Route("/api/ldap/{fullDn}", name="get_ldap_entry", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getLdapEntryByDn(
        string $fullDn,
        Client $ldap,
        SerializerInterface $serializer
    ): JsonResponse {
        // TODO Add attributes option.

        $entries = $ldap->search("(dn=$fullDn)");

        $entry = null;
        if (!empty($entries) && is_array($entries)) {
            $entry = $entries[0];
        }

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

        $result = $ldap->create($dto->getDn(), $dto->getAttributes());
        // TODO Check result.

        return JsonResponse::fromJsonString(
            $serializer->serialize($dto, 'json')
        );
    }

    /**
     * @Route("/api/admin/ldap/{fullDn}", name="edit_ldap_entry", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function editLdapEntryById(
        string $fullDn,
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

        $query = "(dn=$fullDn)";

        // TODO Deserialize from base64 jpegPhoto.

        $result = $ldap->update($query, $dto->getAttributes());
        // TODO Check result.

        return JsonResponse::fromJsonString(
            $serializer->serialize($dto, 'json')
        );
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
        $result = $ldap->delete($fullDn);
        // TODO Check result.

        return new JsonResponse([]);
    }
}
