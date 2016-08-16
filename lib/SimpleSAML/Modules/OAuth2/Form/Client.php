<?php

/**
 * Editor for OAuth2 Client Registry
 *
 * @author Andreas Åkre Solberg <andreas@uninett.no>, UNINETT AS.
 * @package simpleSAMLphp
 */
namespace SimpleSAML\Modules\OAuth2\Form;


class Client
{
	protected function getStandardField($request, &$entry, $key, $multiline = false) {
		if (array_key_exists('field_' . $key, $request)) {
			$entry[$key] = $request['field_' . $key] ;
		} else {
			if (isset($entry[$key])) unset($entry[$key]);
		}
	}

    protected function getArrayField($request, &$entry, $key)
	{
        if (array_key_exists('field_' . $key, $request)) {
            $entry[$key] = preg_split("/[\t\r\n]+/", $request['field_' . $key]);
        } else {
            if (isset($entry[$key])) unset($entry[$key]);
        }
	}

	public function formToMeta($request, $entry = [], $override = NULL) {
		$this->getStandardField($request, $entry, 'name');
		$this->getStandardField($request, $entry, 'description');
		$this->getArrayField($request, $entry, 'redirect_uri');
		$this->getStandardField($request, $entry, 'id');

		if ($override) {
			foreach($override AS $key => $value) {
				$entry[$key] = $value;
			}
		}

		return $entry;
	}

	protected function requireStandardField($request, $key) {
		if (!array_key_exists('field_' . $key, $request))
			throw new \Exception('Required field [' . $key . '] was missing.');
		if (empty($request['field_' . $key]))
			throw new \Exception('Required field [' . $key . '] was empty.');
	}

	public function checkForm($request) {
		$this->requireStandardField($request, 'name');
		$this->requireStandardField($request, 'description');
	}
	

	protected function header($name) {
		return '<tr ><td>&nbsp;</td><td class="header">' . $name . '</td></tr>';
		
	}
	
	protected function readonlyDateField($metadata, $key, $name) {
		$value = '<span style="color: #aaa">Not set</a>';
		if (array_key_exists($key, $metadata))
			$value = date('j. F Y, G:i', $metadata[$key]);
		return '<tr>
			<td class="name">' . $name . '</td>
			<td class="data">' . $value . '</td></tr>';

	}
	
	protected function readonlyField($metadata, $key, $name) {
		$value = '';
		if (array_key_exists($key, $metadata))
			$value = $metadata[$key];
		return '<tr>
			<td class="name">' . $name . '</td>
			<td class="data">' . htmlspecialchars($value) . '</td></tr>';

	}
	
	protected function hiddenField($metadata, $key) {
        if (array_key_exists($key, $metadata)) {
            $value = htmlspecialchars($metadata[$key]);
        } else {
            $value = '';
        }

		return '<input type="hidden" name="field_' . $key . '" value="' . htmlspecialchars($value) . '" />';
	}
	
	protected function flattenLanguageField(&$metadata, $key) {
		if (array_key_exists($key, $metadata)) {
			if (is_array($metadata[$key])) {
				if (isset($metadata[$key]['en'])) {
					$metadata[$key] = $metadata[$key]['en'];
				} else {
					unset($metadata[$key]);
				}
			}
		}
	}

	protected function arrayField($metadata, $key, $name) {
		$value = '';
		if (array_key_exists($key, $metadata)) {
			$value = htmlspecialchars(implode("\n", $metadata[$key]));
		}

		return '<tr><td class="name">' . $name . '</td><td class="data">
		<textarea name="field_' . $key . '" rows="5" cols="50">' . $value . '</textarea></td></tr>';

	}


	protected function standardField($metadata, $key, $name, $textarea = FALSE) {
		if (array_key_exists($key, $metadata)) {
			$value = htmlspecialchars($metadata[$key]);
		} else {
		    $value = '';
        }
		
		if ($textarea) {
			return '<tr><td class="name">' . $name . '</td><td class="data">
			<textarea name="field_' . $key . '" rows="5" cols="50">' . $value . '</textarea></td></tr>';
		} else {
			return '<tr><td class="name">' . $name . '</td><td class="data">
			<input type="text" size="60" name="field_' . $key . '" value="' . $value . '" /></td></tr>';
			
		}
	}

	public function metaToForm($metadata)
    {
		return '<div id="tabdiv">' .
			'<ul>' .
			'<li><a href="#basic">Name and description</a></li>' .
			'</ul>' .
			'<div id="basic"><table class="formtable">' .
				$this->standardField($metadata, 'name', 'Name of client') .
				$this->standardField($metadata, 'description', 'Description of client', true) .
				$this->arrayField($metadata, 'redirect_uri', 'Static/enforcing callback-url (one per line)') .

				$this->hiddenField($metadata, 'id') .
			'</table></div>' .
			'</div>' .
			'<input class="btn" type="submit" name="submit" value="Save" style="margin-top: 5px" />' .
            '<a class="btn" href="registry.php">Return to entity listing <strong>without saving...</strong></a>'
		;
	}
}