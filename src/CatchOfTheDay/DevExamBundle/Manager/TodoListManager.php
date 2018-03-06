<?php

namespace CatchOfTheDay\DevExamBundle\Manager;
use CatchOfTheDay\DevExamBundle\Model\TodoListItem;
class TodoListManager
{
    const DATA_FILE = '@CatchOfTheDayDevExamBundle/Resources/data/todo-list.json';

    /**
     * @var \Symfony\Component\Config\FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @param \Symfony\Component\Config\FileLocatorInterface $fileLocator
     */
    public function __construct($fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * @return string
     */
    private function getDataFilePath()
    {
        return $this->fileLocator->locate(self::DATA_FILE);
    }

    /**
     * @return \CatchOfTheDay\DevExamBundle\Model\TodoListItem[]
     */
    public function read()
    {
        $jsonFile = $this->getDataFilePath();
        $assocArr = json_decode(file_get_contents($jsonFile), true); # decode the JSON into an associative array
        $toDoListItems = [];
        if(!empty($assocArr)){
            foreach($assocArr as $key => $itemArr){
                $toDoListItemObj  = TodoListItem::fromAssocArray($itemArr);
                #Creating ToDoList Object from static function
                $toDoListItems[$key] = $toDoListItemObj;
            }
            return $toDoListItems;
        }else{
            return $toDoListItems;
        }
        // TODO - Parse JSON and translate to array of TodoListItem. Hint: TodoListItem::fromAssocArray()
    }

    /**
     * @param \CatchOfTheDay\DevExamBundle\Model\TodoListItem[] $items
     */
    public function write(array $items)
    {
        $jsonFile = $this->getDataFilePath();
        $arrItems = [];
        if(!empty($items)){
         foreach($items as $key => $itemObj){
             $arrItem = $itemObj->toAssocArray();
             #Here I am making using the object to call toAssocArray, however we can do it with static method too
             $arrItems[$key] = $arrItem;
         }
        }
        if(!empty($arrItems)){
            $jsonObj = json_encode($arrItems);
            # Creating file system obj to insert to file 
            $fs = new \Symfony\Component\Filesystem\Filesystem();
            $fs->dumpFile($jsonFile, $jsonObj);
            return 1;
        }else{
            return 0;
        }
        
        // TODO - Serialise $items to JSON and write to $jsonFile. Hint: TodoListItem::toAssocArray()
    }
}