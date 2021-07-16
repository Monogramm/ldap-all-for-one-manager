<?php

namespace App\DTO;

use RuntimeException;
use Symfony\Component\Ldap\Entry;

/**
 * LDAP Entry Serializer.
 *
 * @see https://symfony.com/doc/current/serializer.html
 */
class LdapEntrySerializer
{

    /**
     * @param Entry|self $ldapEntry LDAP Entry or LDAP entry DTO.
     * @param string $format Serialization format. Can be either 'json' or 'ldif'.
     *
     * @return string Serialized LDAP entry.
     */
    public static function serializeEntry($ldapEntry, string $format = 'json')
    {
        $outputEntry = '';

        switch ($format) {
            case 'ldif':
                $outputEntry = self::toLdif($ldapEntry);
                break;

            case 'json':
                $outputEntry = self::toJson($ldapEntry);
                break;

            default:
                throw new RuntimeException('Unknown format: ' . $format);
                break;
        }

        return $outputEntry;
    }

    /**
     * @param Entry|self $ldapEntry LDAP Entry or LDAP entry DTO.
     *
     * @return string Serialized LDAP entry into JSON.
     */
    private static function toJson($ldapEntry)
    {
        return json_encode($ldapEntry->getAttributes());
    }

    /**
     * @param Entry|self $ldapEntry LDAP Entry or LDAP entry DTO.
     *
     * @return string Serialized LDAP entry into LDIF.
     */
    private static function toLdif($ldapEntry)
    {
        $outputEntry = '';

        $outputEntry = "\n";
        $outputEntry .= 'dn: ' . $ldapEntry->getDn() . "\n";
        foreach ($ldapEntry->getAttributes() as $key => $values) {
            if (empty($values) || ! is_array($values)) {
                continue;
            }
            foreach ($values as $value) {
                $outputEntry .= $key . ': ' . $value . "\n";
            }
        }

        return $outputEntry;
    }

    /**
     * Convert all binary fields from an LDAP entry into a serializable base64 string.
     *
     * @param Entry|self $ldapEntry Ldap entry or LDAP entry DTO.
     *
     * @return Entry|self
     */
    public static function binaryToBase64($ldapEntry)
    {
        foreach ($ldapEntry->getAttributes() as $key => $values) {
            $lowerKey = strtolower($key);

            switch ($lowerKey) {
                case 'jpegphoto':
                    // Serialize binary in base64.
                    self::encodeToBase64($ldapEntry, $key, $values);
                    break;

                default:
                    # Nothing to do
                    break;
            }
        }

        return $ldapEntry;
    }

    /**
     * @param Entry|self $ldapEntry Ldap entry or LDAP entry DTO.
     * @param string $key LDAP attribute key.
     * @param mixed $values LDAP attribute values to convert.
     *
     * @return Entry|self the LDAP Entry whose binary field was converted to base64.
     */
    private static function encodeToBase64($ldapEntry, $key, $values)
    {
        if (!empty($values)) {
            // Serialize binary values in base64.
            $base64 = array();
            foreach ($values as $value) {
                $base64[] = base64_encode($value);
            }
            $ldapEntry->setAttribute($key, $base64);
        }

        return $ldapEntry;
    }

    /**
     * Convert all base64 fields from an LDAP entry into original value.
     *
     * @param Entry|self $ldapEntry Ldap entry or LDAP entry DTO.
     *
     * @return Entry|self
     */
    public static function fromBase64($ldapEntry)
    {
        foreach ($ldapEntry->getAttributes() as $key => $values) {
            $lowerKey = strtolower($key);

            // TODO Check if value is a base64 (use regexp)
            switch ($lowerKey) {
                case 'jpegphoto':
                    // Deserialize base64.
                    self::decodeFromBase64($ldapEntry, $key, $values);
                    break;

                default:
                    # Nothing to do
                    break;
            }
        }

        return $ldapEntry;
    }

    /**
     * @param Entry|self $ldapEntry Ldap entry or LDAP entry DTO.
     * @param string $key LDAP attribute key.
     * @param mixed $values LDAP attribute values to convert.
     *
     * @return Entry|self the LDAP Entry whose binary field was decoded from base64.
     */
    private static function decodeFromBase64($ldapEntry, $key, $values)
    {
        if (!empty($values)) {
            // Serialize binary values in base64.
            $base64 = array();
            foreach ($values as $value) {
                $base64[] = base64_decode($value);
            }
            $ldapEntry->setAttribute($key, $base64);
        }

        return $ldapEntry;
    }
}
