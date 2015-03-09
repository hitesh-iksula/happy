<?php

// echo "outside"; exit;

class Iksula_Explore_Adminhtml_GenderController extends Mage_Adminhtml_Controller_Action
{	
	public function indexAction()
	{
		// echo "index"; exit;
		
		$this->_title($this->__('Manage Gender Options'));
		$this->loadLayout()
			->_setActiveMenu('explore')
			->renderLayout();
			
		// echo "<pre>"; var_dump($this); exit;
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('explore/gender')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('explore_data', $model);
			
			$this->_title($this->__('Explore'))
				->_title($this->__('Manage Genders'));
			if ($model->getId()){
				$this->_title($model->getTitle());
			}else{
				$this->_title($this->__('New Gender'));
			}

			$this->loadLayout();
			$this->_setActiveMenu('explore/gender');

			// $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			// $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('explore/adminhtml_gender_edit'))
				->_addLeft($this->getLayout()->createBlock('explore/adminhtml_gender_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('explore')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function newAction() {
		$this->_forward('edit');
	}
	
	protected function _initItem(){
		if (!Mage::registry('explore_categories')){
			if ($this->getRequest()->getParam('id')){
				Mage::register('explore_categories',
					Mage::getModel('explore/gender')
					->load($this->getRequest()->getParam('id'))->getCategories());
			}
		}
	}
	
	public function categoriesJsonAction()
    {
		$this->_initItem();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('explore/adminhtml_gender_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
	
	public function saveAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			if($data['gender_logo_1']['delete']==1){
				$data['gender_logo_1']='';
			}
			elseif(is_array($data['gender_logo_1'])){
				$data['gender_logo_1']=$data['gender_logo_1']['value'];
			}
			
			if(isset($_FILES['gender_logo_1']['name']) && $_FILES['gender_logo_1']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('gender_logo_1');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					//$path = Mage::getBaseDir('media') . '/explore/gender_logo_1s/' ;
					$path = Mage::getBaseDir() . '/media/explore/gender/';
					$result = $uploader->save($path, $_FILES['gender_logo_1']['name'] );
					$data['gender_logo_1'] = $result['file'];
				} catch (Exception $e) {
					$data['gender_logo_1'] = $_FILES['gender_logo_1']['name'];
		        }
			}

			if($data['gender_logo_2']['delete']==1){
				$data['gender_logo_2']='';
			}
			elseif(is_array($data['gender_logo_2'])){
				$data['gender_logo_2']=$data['gender_logo_2']['value'];
			}
			
			if(isset($_FILES['gender_logo_2']['name']) && $_FILES['gender_logo_2']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('gender_logo_2');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					//$path = Mage::getBaseDir('media') . '/explore/gender_logo_2s/' ;
					$path = Mage::getBaseDir() . '/media/explore/gender/';
					$result = $uploader->save($path, $_FILES['gender_logo_2']['name'] );
					$data['gender_logo_2'] = $result['file'];
				} catch (Exception $e) {
					$data['gender_logo_2'] = $_FILES['gender_logo_2']['name'];
		        }
			}
	  			
			$model = Mage::getModel('explore/gender');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				
				$model->setStores(implode(',',$data['stores']));
				if (isset($data['category_ids'])){
					$model->setCategories(implode(',',array_unique(explode(',',$data['category_ids']))));
				}
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('explore')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('explore')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('explore/gender');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $exploreIds = $this->getRequest()->getParam('explore');
        if(!is_array($exploreIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($exploreIds as $exploreId) {
                    $explore = Mage::getModel('explore/gender')->load($exploreId);
                    $explore->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($exploreIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $exploreIds = $this->getRequest()->getParam('explore');
        if(!is_array($exploreIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($exploreIds as $exploreId) {
                    $explore = Mage::getSingleton('explore/gender')
                        ->load($exploreId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($exploreIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'gender.csv';
        $content    = $this->getLayout()->createBlock('explore/adminhtml_gender_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'gender.xml';
        $content    = $this->getLayout()->createBlock('explore/adminhtml_gender_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
}