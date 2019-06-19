<?php

namespace App\Foundation\Client\SSCardRequest;

use WecarSwoole\Repository\MySQLRepository;

class MySQLSSCardRepository extends MySQLRepository
{
    public function dbAlias(): string
    {
        return 'weicheche';
    }

    public function addKey(Key $key)
    {
        $this->query
            ->insert('wei_keys')
            ->values([
                'wcc_private_key' => $key->WCCPrivateKey,
                'wcc_public_key' => $key->WCCPublicKey,
                'type' => 1,
                'create_time' => time(),
                'is_delete' => 0,
            ])->execute();
    }

    public function getLastOne()
    {
        $info = $this->query->select('*')->from('wei_keys')->orderBy('id desc')->one();
        return new Key($info);
    }
}
