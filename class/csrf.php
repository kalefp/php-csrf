<?php

class CSRF
{
    private $key = 'csrfTokens';
    private $maxHashes = 5;
    private $hashes;

    function __construct()
    {
        $this->load();
    }

    public function input()
    {
    }

    public function validate($hash)
    {
        foreach ($this->hashes as $index => $savedHash) {
            var_dump($savedHash->verify($hash));
            if ($savedHash->verify($hash)) {
                array_splice($this->hashes, $index, 1);
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function generate($expireTime = 0)
    {
        $hash = new Hash($expireTime);

        $this->hashes[] = $hash;
        $this->save();
        return $hash;
    }

    private function load()
    {
        $this->hashes = [];
        if (isset($_SESSION[$this->key])) {
            $sessionHashes = unserialize($_SESSION[$this->key]);
            usort($sessionHashes, function ($a, $b) {
                return $b->createdAt - $a->createdAt;
            });
            foreach ($sessionHashes as $index => $hash) {
                if ($index  < $this->maxHashes) {
                    $this->hashes[] = $hash;
                }
            }
        }
    }

    private function save()
    {
        $_SESSION[$this->key] = serialize($this->hashes);
    }
}


class Hash
{
    private $value;
    private $expireTime;
    public $createdAt;

    function __construct($expireTime)
    {
        $this->value = $this->generate();
        $this->expireTime = $expireTime ? time() + $expireTime : 0;
        $this->createdAt = time();
    }

    private function generate()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    public function expired()
    {
        return time() > $this->expireTime && $this->expireTime;
    }

    public function verify($hash)
    {
        return hash_equals($this->value, $hash) && !$this->expired();
    }
}
