<?php
namespace Seedling\KeyGenerators;

/**
 * CRC32 Key Generator
 */
class Crc32 implements KeyGeneratorInterface
{
    /**
     * @var int $max The maximum supported CRC32 value
     */
    private $max;

    /**
     * Initialize max key size
     *
     * @param int $max
     */
    public function __construct($max = PHP_INT_MAX)
    {
        $this->max = $max;
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($value, $tableName = null)
    {
        $crc = (float)sprintf('%u', crc32($tableName . $value));
        // 32-bit system can't handle unsigned mod using %
        if ($crc > PHP_INT_MAX) {
            $cycles = floor($crc / $this->max);
            return (int)($crc - ($cycles * $this->max));
        }
        return (int)$crc % $this->max;
    }
}
