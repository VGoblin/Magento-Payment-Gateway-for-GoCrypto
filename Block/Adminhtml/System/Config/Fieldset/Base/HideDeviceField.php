<?php
namespace GoCryptoPay\GoCryptoPay\Block\Adminhtml\System\Config\Fieldset\Base;
use GoCryptoPay\GoCryptoPay\Helper\Data;

class HideDeviceField extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Data $dataHelper,
        array $data = []
    ) {
        $this->config = $dataHelper->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('tab1',['label' => __('Cargo Type'),  'class' => 'required-entry']);
        $this->addColumn('tab2',['label' => __('Attribute Set'),  'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        // Disable element if value is inherited from other scope. Flag has to be set before the value is rendered.
        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        $html = '<td class="label"><label for="' .
            $element->getHtmlId() . '"><span' .
            $this->_renderScopeLabel($element) . '>' .
            $element->getLabel() .
            '</span></label></td>';
        $html .= $this->_renderValue($element);

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Decorate field row html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $html)
    {
        $style = 'style="display: none"';
        if($this->config->getValue('payment/gocrypto_pay/client_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) != null && $this->config->getValue('payment/gocrypto_pay/client_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) != null){
            return '<tr id="row_' . $element->getHtmlId().'" ' . $style .'">' . $html . '</tr>';
        }
        return '<tr id="row_' . $element->getHtmlId().'">' . $html . '</tr>';
    }
}
