<?php namespace Cribbb\Domain\Model\Identity;

//use Cribbb\Domain\RecordsEvents;
use Wetzel\Datamapper\Mapping as ORM;
//use Cribbb\Domain\AggregateRoot;
//use Cribbb\Domain\Model\Groups\Group;
//use Doctrine\Common\Collections\ArrayCollection;
//use Cribbb\Domain\Model\Identity\Events\PasswordWasReset;
//use Cribbb\Domain\Model\Identity\Events\UserHasRegistered;
//use Cribbb\Domain\Model\Identity\Events\UsernameWasUpdated;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    //use RecordsEvents;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="following")
     **/
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="following_user_id", referencedColumnName="id")}
     *      )
     */
    private $following;

    /**
     * @ORM\ManyToMany(targetEntity="Cribbb\Domain\Model\Groups\Group", inversedBy="admins")
     * @ORM\JoinTable(name="admins_groups")
     */
    private $adminOf;

    /**
     * @ORM\ManyToMany(targetEntity="Cribbb\Domain\Model\Groups\Group", inversedBy="members")
     * @ORM\JoinTable(name="members_groups")
     */
    private $memberOf;

    /**
     * @ORM\OneToMany(targetEntity="Cribbb\Domain\Model\Discussion\Post", mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="Cribbb\Domain\Model\Identity\Notification", mappedBy="user")
     */
    private $notifications;

    /**
     * Create a new User
     *
     * @param UserId $userId
     * @param Email $email
     * @param Username $username
     * @param HashedPassword $password
     * @return void
     */
    private function __construct(UserId $userId, Email $email, Username $username, HashedPassword $password)
    {
        $this->setId($userId);
        $this->setEmail($email);
        $this->setUsername($username);
        $this->setPassword($password);

        $this->followers     = new ArrayCollection;
        $this->following     = new ArrayCollection;
        $this->adminOf       = new ArrayCollection;
        $this->memberOf      = new ArrayCollection;
        $this->posts         = new ArrayCollection;
        $this->notifications = new ArrayCollection;

        $this->record(new UserHasRegistered);
    }

    /**
     * Register a new User
     *
     * @param UserId $userId
     * @param Email $email
     * @param Username $username
     * @param HashedPassword $password
     * @return User
     */
    public static function register(UserId $userId, Email $email, Username $username, HashedPassword $password)
    {
        $user = new User($userId, $email, $username, $password);

        return $user;
    }

    /**
     * Get the User's id
     *
     * @return UserId
     */
    public function id()
    {
        return UserId::fromString($this->id);
    }

    /**
     * Set the User's id
     *
     * @param UserId $id
     * @return void
     */
    private function setId(UserId $id)
    {
        $this->id = $id->toString();
    }

    /**
     * Get the User's email
     *
     * @return string
     */
    public function email()
    {
        return Email::fromNative($this->email);
    }

    /**
     * Set the User's email
     *
     * @param Email $email
     * @return void
     */
    private function setEmail(Email $email)
    {
        $this->email = $email->toString();
    }

    /**
     * Get the User's username
     *
     * @return string
     */
    public function username()
    {
        return Username::fromNative($this->username);
    }

    /**
     * Set the User's username
     *
     * @param Username $username
     * @return void
     */
    private function setUsername(Username $username)
    {
        $this->username = $username->toString();
    }

    /**
     * Update a User' username
     *
     * @param Username $username
     * @return void
     */
    public function updateUsername(Username $username)
    {
        $this->setUsername($username);

        $this->record(new UsernameWasUpdated);
    }

    /**
     * Set the User's password
     *
     * @param HashedPassword $password
     * @return void
     */
    private function setPassword(HashedPassword $password)
    {
        $this->password = $password->toString();
    }

    /**
     * Reset the User's password
     *
     * @param HashedPassword $password
     * @return void
     */
    public function resetPassword(HashedPassword $password)
    {
        $this->password = $password;

        $this->record(new PasswordWasReset($this));
    }

    /**
     * Follow another User
     *
     * @param User $user
     * @return void
     */
    public function follow(User $user)
    {
        $this->following[] = $user;

        $user->followedBy($this);
    }

    /**
     * Set followed by User
     *
     * @param User $user
     * @return void
     */
    private function followedBy(User $user)
    {
        $this->followers[] = $user;
    }

    /**
     * Return the User's followers
     *
     * @return ArrayCollection
     */
    public function followers()
    {
        return $this->followers;
    }

    /**
     * Return the Users this User is following
     *
     * @return ArrayCollection
     */
    public function following()
    {
        return $this->following;
    }

    /**
     * Unfollow a User
     *
     * @param User $user
     * @return void
     */
    public function unfollow(User $user)
    {
        $this->following->removeElement($user);

        $user->unfollowedBy($this);
    }

    /**
     * Set unfollowed by a User
     *
     * @param User $user
     * @return void
     */
    private function unfollowedBy(User $user)
    {
        $this->followers->removeElement($user);
    }

    /**
     * Add the User as a Member of a Group
     *
     * @param Group $group
     * @return void
     */
    public function addAsMemberOf(Group $group)
    {
        $this->memberOf[] = $group;

        $group->addMember($this);
    }

    /**
     * Return the Groups the User is a member of
     *
     * @return ArrayCollection
     */
    public function memberOf()
    {
        return $this->memberOf;
    }

    /**
     * Return the Groups the User is an admin of
     *
     * @return ArrayCollection
     */
    public function adminOf()
    {
        return $this->adminOf;
    }

    /**
     * Add the User as an Admin of a Group
     *
     * @param Group $group
     * @return void
     */
    public function addAsAdminOf(Group $group)
    {
        $this->adminOf[] = $group;

        $group->addAdmin($this);
    }
}
