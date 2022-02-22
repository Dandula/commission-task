<?php

declare(strict_types=1);

namespace CommissionTask\Repositories\Transactions;

class TransactionRepository
{
    /**
     * @var Persistence $persistence
     */
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function generateId(): TransactionId
    {
        return TransactionId::fromInt($this->persistence->generateId());
    }

    public function findById(TransactionId $id): Transaction
    {
        try {
            $arrayData = $this->persistence->retrieve($id->toInt());
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException(sprintf('Post with id %d does not exist', $id->toInt()), 0, $e);
        }

        return Post::fromState($arrayData);
    }

    public function save(Post $post)
    {
        $this->persistence->persist([
            'id' => $post->getId()->toInt(),
            'statusId' => $post->getStatus()->toInt(),
            'text' => $post->getText(),
            'title' => $post->getTitle(),
        ]);
    }
}
