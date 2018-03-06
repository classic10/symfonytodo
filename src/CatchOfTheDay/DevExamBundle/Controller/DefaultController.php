<?php

namespace CatchOfTheDay\DevExamBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use CatchOfTheDay\DevExamBundle\Model\TodoListItem;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Template
     *
     * @return array
     */
    public function indexAction()
    {
        $manager = $this->get('catch_of_the_day_dev_exam.manager.todo_list');
        $items = $manager->read();

        return [
            'items' => $items,
        ];
    }

    /**
     * @Route("/add", name="add")
     * @Method("POST")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addAction(Request $request)
    {
                
        // TODO - Read the new item's text from $request, add a new TodoListItem to the collection and save.
        $isAjax = $request->isXmlHttpRequest();
        if($isAjax){
            $manager = $this->get('catch_of_the_day_dev_exam.manager.todo_list');
            $items = $manager->read();
            #Fetching name of the item from the post request
            $newToDoItemName = $request->request->get('todoItemName');
            if($newToDoItemName!=""){
                #Checking if item is blank
                $toDoListObj = new TodoListItem();
                $toDoListObj->setText($newToDoItemName);

                array_push($items,$toDoListObj);    /** Insert new collection into array */
                $respWrite = $manager->write($items);
                if($respWrite){
                    $newItems = $manager->read();
                    $newItemHtml = $this->render('CatchOfTheDayDevExamBundle:Default:add.html.twig',array('items' => $newItems,'type'=>"complete"))->getContent();
                    $responseArr = ["status"=>1,"message"=>"Item has been successfully saved.","newItems"=>$newItemHtml];
                }else{
                    $responseArr = ["status"=>0,"message"=>"Some Error Occured, Please Try Again Later."];
                }
                return new JsonResponse($responseArr);
            }else{
                #Returning error message
                return new JsonResponse(['status'=>0,"message"=>"It is not a valid request."]);
            }

        }else{
            return new JsonResponse(['status'=>0,"message"=>"It is not a valid request."]);
        }
    }

    /**
     * @Route("/items/{itemId}/mark-as-complete/{type}", name="mark_as_complete")
     *
     * @param Request $request
     * @param string $itemId
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function markAsCompleteAction(Request $request, $itemId, $type)
    {
        
        // TODO - Look in $items for the item that matches $itemId, update it and save the collection.
        
        $isAjax = $request->isXmlHttpRequest();
        #Checking if Request is AJAX
        if($isAjax){
            # Checking the type of list from where the Ajax request came if it is a completed item list or the to do item list
            if($type=="complete"){

                $setCompleted = true;
                $findCompleted = false;
            }else{
                $setCompleted = false;
                $findCompleted = true;
            }

            $manager = $this->get('catch_of_the_day_dev_exam.manager.todo_list');
            $items = $manager->read();

            #Filtering the Object based on itemId
            $objectToUpdate = array_filter(
                $items,
                function (&$e) use ($itemId,$findCompleted, $setCompleted) {
                   if(($e->getId() == $itemId) && ($e->getComplete()==$findCompleted)){
                       $e->setComplete($setCompleted);
                       return true;
                   }
                }
            );
            
            #If item is found in the json file
            if($objectToUpdate){
                #Saving the collection
                $respWrite = $manager->write($items);
                if($respWrite){
                    #Reading the updated collection
                    $newItems = $manager->read();
                    #Generating view so that view can be updated on AJAX success in javaacript
                    $newItemHtml = $this->render('CatchOfTheDayDevExamBundle:Default:add.html.twig',array('items' => $newItems,'type'=>$type))->getContent();
                    # Here returning proper message to the desired list
                    if($type=="complete"){
                        $responseArr = ["status"=>1,"message"=>"Item has been successfully marked as completed.","newItems"=>$newItemHtml];
                    }else{
                        $responseArr = ["status"=>1,"message"=>"Item has been successfully marked as in-completed.","newItems"=>$newItemHtml];
                    }
                }else{
                    $responseArr = ["status"=>0,"message"=>"Some Error Occured, Please Try Again Later."];
                }
                return new JsonResponse($responseArr);                
            }else{
                #return error message
                return new JsonResponse(['status'=>0,"message"=>"To Do Item Not Found."]);
            }
        }else{
            return new JsonResponse(['status'=>0,"message"=>"It is not a valid request."]);
        }
       
    }

    /**
     * @Route("/completeditems", name="completeditems")
     * @Template
     *
     * @return array
     */
    public function completeditemsAction()
    {
        #Action for the list of items which are completed
        $manager = $this->get('catch_of_the_day_dev_exam.manager.todo_list');
        $items = $manager->read();
        
        return [
            'items' => $items,
        ];
    }

    /**
     * @Route("/edititem/{itemId}", name="edit_to_do_item")
     *
     * @param Request $request
     * @param string $itemId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editItemAction(Request $request, $itemId)
    {
        $isAjax = $request->isXmlHttpRequest();
        if($isAjax){
            
            $manager = $this->get('catch_of_the_day_dev_exam.manager.todo_list');
            $items = $manager->read();
            #Filtering item from the list to fetch the desired item
            $objectToUpdate = array_filter(
                $items,
                function ($e) use ($itemId) {
                   if($e->getId() == $itemId){
                       return $e;
                   }
                }
            );
            
            if(!empty($objectToUpdate)){
                #Rendering the view
                return $this->render('CatchOfTheDayDevExamBundle:Default:edititem.html.twig',array('item' => $objectToUpdate));
            }else{
                return $this->render('CatchOfTheDayDevExamBundle:Default:edititem.html.twig',array('item' => ""));
            }

        }else{
            return new JsonResponse(['status'=>0,"message"=>"It is not a valid request."]);
           
        }
    }

    /**
     * @Route("/updateitem", name="updateitem")
     * @Method("POST")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateitemAction(Request $request)
    {
                
        // TODO - Read the new item's text from $request, add a new TodoListItem to the collection and save.
        $isAjax = $request->isXmlHttpRequest();
        if($isAjax){
            #Reading from the JSON File
            $manager = $this->get('catch_of_the_day_dev_exam.manager.todo_list');
            $items = $manager->read();
            
            #Fetching the variables from post
            $newToDoItemName = $request->request->get('todoItemName');
            $todoItemId = $request->request->get('todoItemId');
            $type = $request->request->get('type');
            
            if($newToDoItemName!="" &&  $todoItemId !=""){

                #Filtering from the collection
                $objectToUpdate = array_filter(
                    $items,
                    function (&$e) use ($todoItemId, $newToDoItemName) {
                       if($e->getId() == $todoItemId){
                           $e->setText($newToDoItemName);
                           return true;
                       }
                    }
                );
                #Writing to the Json file
                $respWrite = $manager->write($items);
                if($respWrite){
                    $newItems = $manager->read(); #Again reading the new collection after updating the collection
                    #Generating view so that it can be updated on AJAX success in Javascript
                    $newItemHtml = $this->render('CatchOfTheDayDevExamBundle:Default:add.html.twig',array('items' => $newItems,'type'=>$type))->getContent();
                    $responseArr = ["status"=>1,"message"=>"Item has been updated successfully.","newItems"=>$newItemHtml];
                }else{
                    $responseArr = ["status"=>0,"message"=>"Some Error Occured, Please Try Again Later."];
                }
                return new JsonResponse($responseArr);
            }else{
                return new JsonResponse(['status'=>0,"message"=>"It is not a valid request."]);
            }

        }else{
            return new JsonResponse(['status'=>0,"message"=>"It is not a valid request."]);
        }
    }


}
