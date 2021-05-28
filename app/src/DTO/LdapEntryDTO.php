<?php

namespace App\DTO;

use RuntimeException;
use Symfony\Component\Ldap\Entry;

class LdapEntryDTO
{
    //TODO Create attrribute associate with LdapEntry
    //TODO Create getter and setter

    /**
     * @var string
     */
    public $dn;

    /**
     * @var array
     */
    public $attributes;

    public function setdn($dn)
    {
        $this->dn = $dn;
    }

     public function getName(): string
    {
        return $this->name;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

     public function getAttributes(): array
    {
        return $this->attributes;
    }

    public static function serializeEntry(Entry $ldapEntry, string $format)
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

    public static function serializeJpegPhoto(Entry $ldapEntry)
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
