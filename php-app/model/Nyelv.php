<?php
declare(strict_types=1);

namespace Model;

class Nyelv
{
    private int $nyelv_id;
    private string $kod;
    private string $megnevezes;

    public function __construct(?int $nyelv_id = null, string $kod = '', string $megnevezes = '')
    {
        $this->nyelv_id = $nyelv_id;
        $this->kod = $kod;
        $this->megnevezes = $megnevezes;
    }

    public function getNyelvId(): ?int
    {
        return $this->nyelv_id;
    }

    public function setNyelvId(?int $nyelv_id): void
    {
        $this->nyelv_id = $nyelv_id;
    }

    public function getKod(): string
    {
        return $this->kod;
    }

    public function setKod(string $kod): void
    {
        $this->kod = $kod;
    }

    public function getMegnevezes(): string
    {
        return $this->megnevezes;
    }

    public function setMegnevezes(string $megnevezes): void
    {
        $this->megnevezes = $megnevezes;
    }

    public static function fromArray(array $data): self
    {
        // Accept keys in different cases: nyelv_id / Nyelv_id
        $map = array_change_key_case($data, CASE_LOWER);
        return new self(
            isset($map['nyelv_id']) ? (int)$map['nyelv_id'] : null,
            $map['kod'] ?? '',
            $map['megnevezes'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'nyelv_id' => $this->nyelv_id,
            'kod' => $this->kod,
            'megnevezes' => $this->megnevezes,
        ];
    }
}