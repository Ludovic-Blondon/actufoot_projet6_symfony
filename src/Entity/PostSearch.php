<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
class PostSearch
{
    /**
     * @var string|null
     * @Assert\Length(
     * min = 3,
     * max = 255,
     * )
     */
    private $keyWord;

    /**
     * @param string|null $keyWord
     * @return PostSearch
     */
    public function setKeyWord(?string $keyWord): PostSearch
    {
        $this->keyWord = $keyWord;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeyWord(): ?string
    {
        return $this->keyWord;
    }

}