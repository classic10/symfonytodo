<?php

namespace CatchOfTheDay\DevExamBundle\Model;

class TodoListItem
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var string
     */
    private $text;

    /**
     * @var bool
     */
    private $complete;

    //private static $staticInstance;

    public function __construct($id=null, $created=null, $complete=null)
    {
        // TODO - Generate an identifier for this TodoListItem
        // Identifier generated at SetId()
        if( $id == "" || $id == null){
            #Generating unique identifier when no Id is passed while creating object
            $id = uniqid(rand(),true);
        }
        $this->id = $id;
        if($created==""||$created==null){
            #Initializing created if no created is passed
            $this->created = new \DateTime();
        }        
        if($complete==""||$complete==null){
            #Initializing complete if no complete is passed
            $this->complete = false;
        }  
      
    }

    /**
     * @return mixed
     */
    public function getComplete()
    {
        return $this->complete;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $complete
     * @return TodoListItem
     */
    public function setComplete($complete)
    {
        $this->complete = $complete;

        return $this;
    }

    /**
     * @param \DateTime $created
     * @return TodoListItem
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @param string $id
     * @return TodoListItem
     */
    public function setId($id)
    {
        if( $id==""|| $id == null){
            $id = uniqid(rand(),true);
        }
        $this->id = $id;
        
        return $this;
    }

    /**
     * @param string $text
     * @return TodoListItem
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param array $data
     * @return TodoListItem
     */
    public static function fromAssocArray(array $data)
    {
        
        // TODO
       #Creating Object of the same class as its a static function
       $toDoListItemObj = new self; 
       $toDoListItemObj->id = $data['id'];
       $toDoListItemObj->text=$data['text'];
       $toDoListItemObj->created = new \DateTime($data['created']['date']);
       $toDoListItemObj->complete = $data['complete'];
       return $toDoListItemObj;
    }

    /**
     * @return array
     */
    public function toAssocArray()
    {
        $data = [];
        # We can make this function static too but here it was not written static, so I am assuming that its not static
        // TODO
        $data['id'] = $this->id;
        $data['created'] = $this->created;
        $data['text'] =$this->text;
        $data['complete'] = $this->complete;

        return $data;
    }
}