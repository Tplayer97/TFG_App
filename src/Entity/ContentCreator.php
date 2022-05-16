<?php

namespace App\Entity;

use App\Repository\ContentCreatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContentCreatorRepository::class)
 */
class ContentCreator
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $Nombre;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $Contraseña;

    /**
     * @ORM\OneToMany(targetEntity=Content::class, mappedBy="contentCreator")
     */
    private $Content;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="contentCreator")
     */
    private $Comments;

    public function __construct()
    {
        $this->Content = new ArrayCollection();
        $this->Comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->Nombre;
    }

    public function setNombre(string $Nombre): self
    {
        $this->Nombre = $Nombre;

        return $this;
    }

    public function getContraseña(): ?string
    {
        return $this->Contraseña;
    }

    public function setContraseña(string $Contraseña): self
    {
        $this->Contraseña = $Contraseña;

        return $this;
    }

    /**
     * @return Collection<int, Content>
     */
    public function getContent(): Collection
    {
        return $this->Content;
    }

    public function addContent(Content $content): self
    {
        if (!$this->Content->contains($content)) {
            $this->Content[] = $content;
            $content->setContentCreator($this);
        }

        return $this;
    }

    public function removeContent(Content $content): self
    {
        if ($this->Content->removeElement($content)) {
            // set the owning side to null (unless already changed)
            if ($content->getContentCreator() === $this) {
                $content->setContentCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->Comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comments->contains($comment)) {
            $this->Comments[] = $comment;
            $comment->setContentCreator($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getContentCreator() === $this) {
                $comment->setContentCreator(null);
            }
        }

        return $this;
    }
}
