<?php
namespace Singular\Crud;

/**
 * Class Field.
 *
 * @package Singular\Crud
 *
 * @author Otávio Fernandes <otavio@neton.com.br>
 */
class Field
{
    /**
     * Tipo de campo.
     *
     * @var string
     */
    protected $type;

    /**
     * Nome do campo.
     *
     * @var string
     */
    protected $name;

    /**
     * Tamanho do campo.
     *
     * @var int
     */
    protected $length;

    /**
     * Precisão do campo decimal.
     *
     * @var int
     */
    protected $precision = 2;

    /**
     * Casas decimais do campo.
     *
     * @var int
     */
    protected $scale;

    /**
     * Se o campo aceita ou não nulo.
     *
     * @var bool
     */
    protected $notnull = false;

    /**
     * Se o campo é ou não auto-incremento.
     *
     * @var bool
     */
    protected $autoincrement;

    /**
     * Valor default do campo.
     *
     * @var mixed
     */
    protected $default;

    /**
     * Número da linha em que o campo será adicionado no formulário.
     *
     * @var int
     */
    protected $row = 1000;

    /**
     * Tamanho da coluna do campo no formulário (bootstrap).
     *
     * @var int
     */
    protected $col = 12;

    /**
     * Tamanho mínimo do valor do campo no formulário.
     *
     * @var int
     */
    protected $min = 0;

    /**
     * Tamanho máximo do valor do campo no formulário.
     *
     * @var int
     */
    protected $max = 0;

    /**
     * Opções de valores que o campo irá aceitar (campo do tipo lista).
     *
     * @var array
     */
    protected $options = array();

    /**
     * Tipo de campo que será renderizado no formulário.
     *
     * @var string
     */
    protected $input = null;

    /**
     * Se o campo será ou não exibido no formulário.
     *
     * @var bool
     */
    protected $form = true;

    /**
     * Ordem de exibição do campo.
     *
     * @var mixed
     */
    protected $order = null;

    /**
     * Label de exibição do campo no formulário.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Checkbox valor verdadeiro.
     *
     * @var string
     */
    protected $trueValue = '';

    /**
     * Checkbox valor falso.
     *
     * @var string
     */
    protected $falseValue = '';


    /**
     * Inicializa o objeto campo.
     *
     * @param array $params
     */
    public function __construct($values)
    {
        $this->setValues($values);

        if ($this->input == null) {
            $this->defineInputType();
        }

        if ($this->label == '') {
            $this->label = ucfirst($this->name);
        }

    }

    /**
     * Magic method usado para recuperar as proprieades protegidas da classe.
     *
     * @param string $name
     *
     * @return string
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * Retorna a renderização do campo.
     *
     * @return string
     */
    public function render()
    {
        $method = 'render'.ucfirst($this->input);

        $def = array();

        $def[] = 'name="'.$this->name.'"';

        if ($this->notnull && !$this->autoincrement){
            $def[] = 'required';
        }

        if ($this->autoincrement){
            $def[] = 'readonly';
        }

        if ($this->max > 0) {
            $def[] = 'ng-maxlength="'.$this->max.'"';
        }

        if ($this->min > 0) {
            $def[] = 'ng-minlength="'.$this->min.'"';
        }

        if ($this->trueValue != '')
            $def[] = 'true-value="'.$this->trueValue.'"';

        if ($this->falseValue != '')
            $def[] = 'false-value="'.$this->falseValue.'"';

        //if ($this->precision > 0){
            $def[] = 'precision="'.$this->scale  .'"';
        //}

        $def[] = 'label = "'.$this->label.'"';
        $field = $this->$method($def);

        return sprintf('<div class="col-md-%d">%s</div>', $this->col, $field);

        return sprintf(
            '<div class="col-md-%d"><div class="form-group"><label class="control-label">%s</label>%s</div></div>',
            $this->col,
            $this->label,
            $field
        );
    }

    /**
     * Renderiza um campo do tipo numérico.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderNumber($def)
    {
        $def[] = 'type="number"';

        $def = implode(' ', $def);

        $tpl = "<sing-form-number $def></sing-form-number>";

        return $tpl;
    }

    /**
     * Renderiza um campo do texto.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderText($def)
    {
        $def = implode(' ', $def);

        $tpl = "<sing-form-text $def></sing-form-text>";

        return $tpl;
    }

    /**
     * Renderiza um campo do tipo textarea.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderTextarea($def)
    {
        $def = implode(' ', $def);

        $tpl = "<sing-form-textarea $def/></sing-form-textarea>";

        return $tpl;
    }

    /**
     * Renderiza um campo do tipo select.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderSelect($def)
    {
        $def[] = 'options = '.(json_encode($this->options));

        $def = implode(' ', $def);

        //die($def);
        $tpl = "<sing-form-select $def/></sing-form-select>";

        return $tpl;
    }


    /**
     * Renderiza um campo do tipo float.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderDecimal($def)
    {
        $def[] = 'type="text"'; //@todo: implementar diretiva de campo do tipo float

        $def = implode(' ', $def);

        $tpl = "<sing-form-decimal $def></sing-form-decimal>";

        return $tpl;
    }

    /**
     * Renderiza um campo do tipo date.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderDate($def)
    {
        $def = implode(' ', $def);

        $tpl = "<sing-form-date $def></sing-form-date>";

        return $tpl;
    }

    /**
     * Renderiza um campo do tipo checkbox.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderCheckbox($def)
    {
        $def = implode(' ', $def);

        $tpl = "<sing-form-checkbox $def></sing-form-checkbox>";

        return $tpl;
    }


    /**
     * Renderiza um campo do tipo hidden.
     *
     * @param array $def
     *
     * @return string
     */
    private function renderHidden($def)
    {
        $def = implode(' ', $def);

        $tpl = "<input type='hidden' ng-model='record.{$this->name}' $def />";

        return $tpl;
    }

    /**
     * Define o tipo de campo de acordo com o tipo de campo.
     */
    private function defineInputType()
    {

        $input = '';

        switch ($this->type) {
            case 'integer':
                $input = 'number';
            break;
            case 'datetime':
                $input = 'datetime';
            break;
            case 'date':
                $input = 'date';
            break;
            case 'text':
                $input = 'textarea';
            break;
            case 'float':
                $input = 'decimal';
            break;
            case 'decimal':
                $input = 'decimal';
            break;
            case 'string':
                $input = 'text';
            break;
            case 'combo':
                $input = 'select';
            break;
            case 'hidden':
                $input = 'hidden';
                $this->notnull = false;
            break;
            case 'checkbox':
                $input = 'checkbox';
            break;

        }

        $this->input = $input;
    }

    /**
     * Seta os valores das propriedades da classe.
     *
     * @param array $values
     */
    private function setValues($values)
    {
        //echo "<pre>";
        //print_r($values);
        //echo "</pre>";
        foreach ($values as $field => $value) {
            $this->$field = $value;
        }

        //echo "<pre>";
        //print_r($this);
        //echo "</pre>";
        //die();

    }
} 