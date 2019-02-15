<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=8,
     *      max=255,
     *      minMessage = "The note has to contain at least {{ limit }} characters.",
     *      maxMessage = "The note cannot contain more than {{ limit }} characters."
     *      )
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="sharedNotes")
     */
    private $sharedToUsers;

    /**
     * @ORM\Column(type="string", length=191)
     * @Assert\NotBlank()
     * @Assert\Length(min=8,
     *      max=40,
     *      minMessage = "Title has to contain at least {{ limit }} characters.",
     *      maxMessage = "Title cannot contain more than {{ limit }} characters."
     *      )
     */
    private $title;

    public function __construct()
    {
        $this->sharedToUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSharedToUsers(): Collection
    {
        return $this->sharedToUsers;
    }

    public function addSharedToUser(User $sharedToUser): self
    {
        if (!$this->sharedToUsers->contains($sharedToUser)) {
            $this->sharedToUsers[] = $sharedToUser;
        }

        return $this;
    }

    public function removeSharedToUser(User $sharedToUser): self
    {
        if ($this->sharedToUsers->contains($sharedToUser)) {
            $this->sharedToUsers->removeElement($sharedToUser);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
