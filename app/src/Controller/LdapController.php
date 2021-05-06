<?php

namespace App\Controller;

use App\Service\Ldap\Client;
use Error;
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
        //$query = json_decode($request->get('query', '{}'), true);
        // TODO Add order option.
        //$orders  = json_decode($request->get('orders', null), true);
        // TODO Serialize in base64 jpegPhoto.
        // TODO Add attributes option.

        $query = $request->get('query', '()');
        $attributes = $request->get('attributes', [""]);
        
        $ldapEntries = $ldap->search($query);

        foreach ($ldapEntries as $key => $entry) {
            foreach ($attributes as $attr) {
                $entries[$key][$attr] = ($entry->hasAttribute($attr) && !empty($entry->getAttribute($attr))) ?
                    json_encode($entry->getAttribute($attr)) : "null";
            }
        }

        $total = count($entries);

        $entries = $serializer->serialize($entries, 'json');

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
        if($result)
        {
            return JsonResponse::fromJsonString(
                $serializer->serialize($dto, 'json')
            );
        }

        return new Error("Operation échouer");
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

        // TODO Check result.
        if($ldap->update("($query)", $dto->getAttributes()))
        {
            return JsonResponse::fromJsonString(
                $serializer->serialize($dto, 'json')
            );
        }

        return new Error("Operation échouer");
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
        if($result)
        {
            return new JsonResponse(['result'=>$result]);
        }

        return new Error("Operation échouer");
    }
}
