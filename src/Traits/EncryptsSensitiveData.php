<?php

namespace CyberSec\Shield\Traits;

use Illuminate\Support\Facades\Crypt;

trait EncryptsSensitiveData
{
    /**
     * Get the attributes that should be encrypted.
     * Define an $encryptable array on the model using this trait.
     *
     * @return array
     */
    public function getEncryptableAttributes()
    {
        return property_exists($this, 'encryptable') ? $this->encryptable : [];
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getEncryptableAttributes()) && !is_null($value)) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                // If decryption fails, it might not be encrypted yet, or the key changed.
                // In a robust system, you'd log this or handle key rotation.
            }
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getEncryptableAttributes()) && !is_null($value)) {
            $value = Crypt::encryptString($value);
        }

        return parent::setAttribute($key, $value);
    }
    
    /**
     * Override the toArray method so encrypted fields aren't dumped in plain text
     * unless explicitly handled.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        foreach ($this->getEncryptableAttributes() as $key) {
            if (array_key_exists($key, $array)) {
                $array[$key] = '[ENCRYPTED_DATA]'; // Mask it by default in array output
            }
        }
        
        return $array;
    }
}
