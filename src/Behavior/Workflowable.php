<?php


namespace App\Behavior;


trait Workflowable
{
    /**
     * @MongoDB\Field(type="string")
     *
     * @var string
     */
    protected $state;

    /**
     * @return string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return Workflowable
     */
    public function setState(string $state,  $context = []): self
    {
        $this->state = $state;

        return $this;
    }
}
