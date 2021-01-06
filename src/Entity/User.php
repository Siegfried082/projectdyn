<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastLogAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDisabled;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Bans::class, mappedBy="users")
     */
    private $bans;

    /**
     * @ORM\OneToMany(targetEntity=CourseRegister::class, mappedBy="user")
     */
    private $courseRegisters;

    /**
     * @ORM\OneToMany(targetEntity=Course::class, mappedBy="teacher")
     */
    private $courses;

    /**
     * @ORM\OneToMany(targetEntity=UserMessage::class, mappedBy="user")
     */
    private $userMessages;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->bans = new ArrayCollection();
        $this->courseRegisters = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->userMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRolesName(): string
    {

        $roles = $this->roles;

        if ($roles[0] == 'ROLE_USER') {
            $name = 'Utilisateur';
        }
        else if ($roles[0] == 'ROLE_ADMIN') {
            $name = 'Administrateur';
        }
        return $name;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        $lastname= $this->lastname;
        $firstname= $this->firstname;

        $name = $firstname.' '.$lastname;
        return $name;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLastLogAt(): ?\DateTimeInterface
    {
        return $this->lastLogAt;
    }

    public function setLastLogAt(\DateTimeInterface $lastLogAt): self
    {
        $this->lastLogAt = $lastLogAt;

        return $this;
    }

    public function getIsDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setIsDisabled(bool $isDisabled): self
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bans[]
     */
    public function getBans(): Collection
    {
        return $this->bans;
    }

    public function addBan(Bans $ban): self
    {
        if (!$this->bans->contains($ban)) {
            $this->bans[] = $ban;
            $ban->setUsers($this);
        }

        return $this;
    }

    public function removeBan(Bans $ban): self
    {
        if ($this->bans->removeElement($ban)) {
            // set the owning side to null (unless already changed)
            if ($ban->getUsers() === $this) {
                $ban->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CourseRegister[]
     */
    public function getCourseRegisters(): Collection
    {
        return $this->courseRegisters;
    }

    public function addCourseRegister(CourseRegister $courseRegister): self
    {
        if (!$this->courseRegisters->contains($courseRegister)) {
            $this->courseRegisters[] = $courseRegister;
            $courseRegister->setUser($this);
        }

        return $this;
    }

    public function removeCourseRegister(CourseRegister $courseRegister): self
    {
        if ($this->courseRegisters->removeElement($courseRegister)) {
            // set the owning side to null (unless already changed)
            if ($courseRegister->getUser() === $this) {
                $courseRegister->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Course[]
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->setTeacher($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getTeacher() === $this) {
                $course->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserMessage[]
     */
    public function getUserMessages(): Collection
    {
        return $this->userMessages;
    }

    public function addUserMessage(UserMessage $userMessage): self
    {
        if (!$this->userMessages->contains($userMessage)) {
            $this->userMessages[] = $userMessage;
            $userMessage->setUser($this);
        }

        return $this;
    }

    public function removeUserMessage(UserMessage $userMessage): self
    {
        if ($this->userMessages->removeElement($userMessage)) {
            // set the owning side to null (unless already changed)
            if ($userMessage->getUser() === $this) {
                $userMessage->setUser(null);
            }
        }

        return $this;
    }




}
