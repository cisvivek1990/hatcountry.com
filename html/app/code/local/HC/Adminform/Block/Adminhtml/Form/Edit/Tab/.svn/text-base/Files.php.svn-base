<?php
/**
 * Created by PhpStorm.
 * User: kbordyug
 * Date: 2/20/2015
 * Time: 1:07 PM
 */
class HC_Adminform_Block_Adminhtml_Form_Edit_Tab_Files extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('adminform_files');
    }

    protected function _prepareCollection()
    {
        $collection = $this->getImages();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function getImages()
    {
        $helper = Mage::helper('hc_adminform');
        $dir = $helper->getAbsoluteUploadPath();

        $collection = new Varien_Data_Collection();

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' && $file != '..') {
                        $rowObj = new Varien_Object();
                        $fileurl = $helper->getRelativePath() . $file;

                        $rowObj->setData('filename', $file);
                        $rowObj->setData('url', $fileurl);
                        $collection->addItem($rowObj);
                    }
                }
                closedir($dh);
            }
        }
        return $collection;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('image', array(
            'header' => 'Image',
            'sortable' => false,
            'filter' => false,
            'width' => '60',
            'index' => 'url',
            'renderer' => 'HC_Adminform_Block_Adminhtml_Form_Edit_Renderer_Image'
        ));

        $this->addColumn('filename', array(
            'header' => 'Url',
            'filter' => false,
            'sortable' => false,
            'width' => '60',
            'index' => 'url'
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Delete'),
                        'url'     => array(
                            'base'=>'*/*/delete',
                        ),
                        'field'   => 'filename',
                        'confirm' => 'Are you sure?'
                    ),
                    array(
                        'caption' => Mage::helper('sales')->__('Open in new window'),
                        'popup'   => true,
                        'url'     => array('base'=>'*/*/open'),
                        'field'   => 'filename',
        )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'filename',
            ));

        return parent::_prepareColumns();
    }


    /**
     * get URL for Ajax call
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }


}