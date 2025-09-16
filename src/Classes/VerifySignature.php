<?php

namespace Vdhoangson\ZmpOpenApi\Classes;

class VerifySignature
{
    public static function generateSignature($data, $apiKey)
    {
        try {
            if (!$data || !is_array($data)) {
                throw new \Exception('Data must be an array');
            }
            if ($data['timestamp'] < 0) {
                throw new \Exception('Timestamp invalid');
            }

            $keys = array_keys($data);

            sort($keys);

            $content = '';
            foreach ($keys as $k) {
                $value = $data[$k];
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $content .= $value;
            }

            $signature = hash('sha256', $content . $apiKey);
            return $signature;
        } catch (\Exception $e) {
            $error = new \Exception($e->getMessage());
            throw $error;
        }
    }
}
