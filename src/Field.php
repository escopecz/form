<?php
/**
 * Part of the Joomla! Framework Form Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Form;


use Joomla\String\String;
use Joomla\String\Normalise;
use Joomla\Language\Text;
use SimpleXMLElement;

/**
 * Abstract Form Field class for the Joomla! Framework.
 *
 * @since  1.0
 */
abstract class Field
{
	/**
	 * The description text for the form field.  Usually used in tooltips.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $description;

	/**
	 * The SimpleXMLElement object of the <field /> XML element that describes the form field.
	 *
	 * @var    SimpleXMLElement
	 * @since  1.0
	 */
	protected $element;

	/**
	 * The Form object of the form attached to the form field.
	 *
	 * @var    Form
	 * @since  1.0
	 */
	protected $form;

	/**
	 * The form control prefix for field names from the Form object attached to the form field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $formControl;

	/**
	 * The hidden state for the form field.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $hidden = false;

	/**
	 * True to translate the field label string.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $translateLabel = true;

	/**
	 * True to translate the field description string.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $translateDescription = true;

	/**
	 * True to translate the field's options.
	 *
	 * @var    boolean
	 * @since  __DEPLOY_VERSION__
	 */
	protected $translateOptions = true;

	/**
	 * The document id for the form field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $id;

	/**
	 * The input for the form field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $input;

	/**
	 * The label for the form field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $label;

	/**
	 * The multiple state for the form field.
	 *
	 * If true then multiple values are allowed for the field.  Most often used for list field types.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $multiple = false;

	/**
	 * The name of the form field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name;

	/**
	 * The name of the field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $fieldname;

	/**
	 * The group of the field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $group;

	/**
	 * The required state for the form field.
	 *
	 * If true then there must be a value for the field to be considered valid.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $required = false;

	/**
	 * The disabled state for the form field.
	 *
	 * If true then there must not be a possibility to change the pre-selected value, and the value must not be submitted by the browser.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $disabled = false;

	/**
	 * The readonly state for the form field.
	 *
	 * If true then there must not be a possibility to change the pre-selected value, and the value must submitted by the browser.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $readonly = false;

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type;

	/**
	 * The validation method for the form field.
	 *
	 * This value will determine which method is used to validate the value for a field.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $validate;

	/**
	 * The value of the form field.
	 *
	 * @var    mixed
	 * @since  1.0
	 */
	protected $value;

	/**
	 * The label's CSS class of the form field
	 *
	 * @var    mixed
	 * @since  1.0
	 */
	protected $labelClass;

	/**
	 * Container for the Text object
	 *
	 * @var    Text
	 * @since  __DEPLOY_VERSION__
	 */
	private $text;

	/**
	 * The count value for generated name field
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected static $count = 0;

	/**
	 * The string used for generated fields names
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $generated_fieldname = '__field';

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   Form  $form  The form to attach to the form field object.
	 *
	 * @since   1.0
	 */
	public function __construct(Form $form = null)
	{
		// If there is a form passed into the constructor set the form and form control properties.
		if ($form instanceof Form)
		{
			$this->form = $form;
			$this->formControl = $form->getFormControl();
		}

		// Detect the field type if not set
		if (!isset($this->type))
		{
			$parts = explode('\\', get_called_class());

			if ($parts[0] != 'J')
			{
				$this->type = ucfirst($parts[0]);
			}
			else
			{
				$this->type = '';
			}

			for($i = 1; $i < count($parts) && $parts[$i] != "Field"; $i++);

			for(; $i < count($parts); $i++)
			{
				$this->type .= '\\' . String::ucfirst($parts[$i]);
			}
		}
	}

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   1.0
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'description':
			case 'formControl':
			case 'hidden':
			case 'id':
			case 'multiple':
			case 'name':
			case 'required':
			case 'disabled':
			case 'readonly':
			case 'type':
			case 'validate':
			case 'value':
			case 'labelClass':
			case 'fieldname':
			case 'group':
				return $this->$name;

			case 'input':
				// If the input hasn't yet been generated, generate it.
				if (empty($this->input))
				{
					$this->input = $this->getInput();
				}

				return $this->input;

			case 'label':
				// If the label hasn't yet been generated, generate it.
				if (empty($this->label))
				{
					$this->label = $this->getLabel();
				}

				return $this->label;

			case 'title':
				return $this->getTitle();
		}

		return null;
	}

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   Form  $form  The Form object to attach to the form field.
	 *
	 * @return  Field  The form field object so that the method can be used in a chain.
	 *
	 * @since   1.0
	 */
	public function setForm(Form $form)
	{
		$this->form = $form;
		$this->formControl = $form->getFormControl();

		return $this;
	}

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		// Make sure there is a valid FormField XML element.
		if ((string) $element->getName() != 'field')
		{
			return false;
		}

		// Reset the input and label values.
		$this->input = null;
		$this->label = null;

		// Set the XML element object.
		$this->element = $element;

		// Get some important attributes from the form field element.
		$class = (string) $element['class'];
		$id = (string) $element['id'];
		$multiple = (string) $element['multiple'];
		$name = (string) $element['name'];
		$required = (string) $element['required'];
		$disabled = (string) $element['disabled'];
		$readonly = (string) $element['readonly'];

		// Set the required, disabled and validation options.
		$this->required = $required == 'true';
		$this->disabled = $disabled == 'true';
		$this->readonly = $readonly == 'true';
		$this->validate = (string) $element['validate'];

		// Set the multiple values option.
		$this->multiple = ($multiple == 'true' || $multiple == 'multiple');

		// Allow for field classes to force the multiple values option.
		if (isset($this->forceMultiple))
		{
			$this->multiple = (bool) $this->forceMultiple;
		}

		// Set the field description text.
		$this->description = (string) $element['description'];

		// Set the visibility.
		$this->hidden = ((string) $element['type'] == 'hidden' || (string) $element['hidden'] == 'true');

		// Determine whether to translate the field label, description, and options.
		$this->translateLabel = !(string) $this->element['translate_label'] == 'false';
		$this->translateDescription = !(string) $this->element['translate_description'] == 'false';
		$this->translateOptions = !(string) $this->element['translate_options'] == 'false';

		// Set the group of the field.
		$this->group = $group;

		// Set the field name and id.
		$this->fieldname = $this->getFieldName($name);
		$this->name = $this->getName($this->fieldname);
		$this->id = $this->getId($id, $this->fieldname);

		// Set the field default value.
		$this->value = $value;

		// Set the CSS class of field label
		$this->labelClass = (string) $element['labelclass'];

		return true;
	}

	/**
	 * Method to get the id used for the field input tag.
	 *
	 * @param   string  $fieldId    The field element id.
	 * @param   string  $fieldName  The field element name.
	 *
	 * @return  string  The id to be used for the field input tag.
	 *
	 * @since   1.0
	 */
	protected function getId($fieldId, $fieldName)
	{
		$id = '';

		// If there is a form control set for the attached form add it first.
		if ($this->formControl)
		{
			$id .= $this->formControl;
		}

		// If the field is in a group add the group control to the field id.
		if ($this->group)
		{
			// If we already have an id segment add the group control as another level.
			if ($id)
			{
				$id .= '_' . str_replace('.', '_', $this->group);
			}
			else
			{
				$id .= str_replace('.', '_', $this->group);
			}
		}

		// If we already have an id segment add the field id/name as another level.
		if ($id)
		{
			$id .= '_' . ($fieldId ? $fieldId : $fieldName);
		}
		else
		{
			$id .= ($fieldId ? $fieldId : $fieldName);
		}

		// Clean up any invalid characters.
		$id = preg_replace('#\W#', '_', $id);

		return $id;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0
	 */
	abstract protected function getInput();

	/**
	 * Retrieves the Text object
	 *
	 * @return  Text
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function getText()
	{
		if (!($this->text instanceof Text))
		{
			throw new \RuntimeException('A Joomla\\Language\\Text object is not set.');
		}

		return $this->text;
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   1.0
	 */
	protected function getTitle()
	{
		$title = '';

		if ($this->hidden)
		{
			return $title;
		}

		// Get the label text from the XML element, defaulting to the element name.
		$title = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$title = $this->translateLabel ? $this->getText()->translate($title) : $title;

		return $title;
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   1.0
	 */
	protected function getLabel()
	{
		$label = '';

		if ($this->hidden)
		{
			return $label;
		}

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? $this->getText()->translate($text) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		$class = $this->required == true ? $class . ' required' : $class;
		$class = !empty($this->labelClass) ? $class . ' ' . $this->labelClass : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			$label .= ' title="'
				. htmlspecialchars(
				trim($text, ':') . '::' . ($this->translateDescription ? $this->getText()->translate($this->description) : $this->description),
				ENT_COMPAT, 'UTF-8'
			) . '"';
		}

		// Add the label text and closing tag.
		$label .= '>' . $text . '</label>';

		return $label;
	}

	/**
	 * Method to get the name used for the field input tag.
	 *
	 * @param   string  $fieldName  The field element name.
	 *
	 * @return  string  The name to be used for the field input tag.
	 *
	 * @since   1.0
	 */
	protected function getName($fieldName)
	{
		$name = '';

		// If there is a form control set for the attached form add it first.
		if ($this->formControl)
		{
			$name .= $this->formControl;
		}

		// If the field is in a group add the group control to the field name.
		if ($this->group)
		{
			// If we already have a name segment add the group control as another level.
			$groups = explode('.', $this->group);

			if ($name)
			{
				foreach ($groups as $group)
				{
					$name .= '[' . $group . ']';
				}
			}
			else
			{
				$name .= array_shift($groups);

				foreach ($groups as $group)
				{
					$name .= '[' . $group . ']';
				}
			}
		}

		// If we already have a name segment add the field name as another level.
		if ($name)
		{
			$name .= '[' . $fieldName . ']';
		}
		else
		{
			$name .= $fieldName;
		}

		// If the field should support multiple values add the final array segment.
		if ($this->multiple)
		{
			$name .= '[]';
		}

		return $name;
	}

	/**
	 * Method to get the field name used.
	 *
	 * @param   string  $fieldName  The field element name.
	 *
	 * @return  string  The field name
	 *
	 * @since   1.0
	 */
	protected function getFieldName($fieldName)
	{
		if ($fieldName)
		{
			return $fieldName;
		}
		else
		{
			self::$count = self::$count + 1;

			return self::$generated_fieldname . self::$count;
		}
	}

	/**
	 * Sets the Text object
	 *
	 * @param   Text  $text  The Text object to store
	 *
	 * @return  Field  Instance of this class.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setText(Text $text)
	{
		$this->text = $text;

		return $this;
	}
}
