<?php
class User {
    public int $UserId;
    public string $Login;
    public string $Nev;

    public function __construct(int $UserId, string $Login, string $Nev) {
        $this->UserId = $UserId;
        $this->Login = $Login;
        $this->Nev = $Nev;
    }
}
