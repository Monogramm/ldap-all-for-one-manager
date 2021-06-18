<?php

namespace App\DTO;

use RuntimeException;

class LdapEntryDTO
{
    private $dn;
    private $attributes;

    /**
     * Returns the entry's DN.
     *
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * Returns whether an attribute exists.
     *
     * @param string $name The name of the attribute
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Returns a specific attribute's value.
     *
     * As LDAP can return multiple values for a single attribute,
     * this value is returned as an array.
     *
     * @param string $name The name of the attribute
     *
     * @return array|null
     */
    public function getAttribute($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Returns the complete list of attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets a value for the given attribute.
     *
     * @param string $name
     */
    public function setAttribute($name, array $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Removes a given attribute.
     *
     * @param string $name
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * @param string $format
     * 
     * @return string
     */
    public function serialize(string $format)
    {
        return self::serializeEntry($this, $format);
    }

    /**
     * @param Entry|LdapEntryDTO $ldapEntry 
     * @param string $format
     * 
     * @return string
     */
    public static function serializeEntry($ldapEntry, string $format = 'json')
    {
        $outputEntry = '';

        switch ($format) {
            case 'ldif':
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
                break;

            case 'json':
                $outputEntry = json_encode($ldapEntry->getAttributes());
                break;

            default:
                throw new RuntimeException('Unknown format: ' . $format);
                break;
        }

        return $outputEntry;
    }

    /**     
     * @param Entry|LdapEntryDTO $ldapEntry
     * @param string $format
     * 
     * @return string
     */
    public static function serializeJpegPhoto($ldapEntry)
    {
        // Serialize in base64 jpegPhoto.
        if (!empty($ldapEntry->hasAttribute('jpegPhoto')) && !empty($ldapEntry->getAttribute('jpegPhoto'))) {
            // Serialize in base64 jpegPhoto.
            $jpegPhotos = array();
            foreach ($ldapEntry->getAttribute('jpegPhoto') as $jpegPhoto) {
                $jpegPhotos[] = base64_encode($jpegPhoto);
            }
            $ldapEntry->setAttribute('jpegPhoto', $jpegPhotos);
        }

        return $ldapEntry;
    }
}
