<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\data\storage\xml;

use Exception;
use qtism\data\storage\StorageException;

/**
 * An exception type that represent an error when dealing with QTI-XML files.
 */
class XmlStorageException extends StorageException
{
    /**
     * An array containing libxml errors.
     *
     * @var LibXmlErrorCollection
     */
    private $errors = null;

    /**
     * Create a new XmlStorageException object.
     *
     * @param string $message A human-readable message describing the exception.
     * @param Exception $previous An optional previous exception which is the cause of this one.
     * @param array $errors An array of errors (stdClass) as generated by libxml_get_errors().
     */
    public function __construct($message, $previous = null, LibXmlErrorCollection $errors = null)
    {
        parent::__construct($message, 0, $previous);

        if (empty($errors)) {
            $errors = new LibXmlErrorCollection();
        }

        $this->setErrors($errors);
    }

    /**
     * Set the errors returned by libxml_get_errors.
     *
     * @param LibXmlErrorCollection $errors A collection of LibXMLError objects.
     */
    protected function setErrors(LibXmlErrorCollection $errors = null)
    {
        $this->errors = $errors;
    }

    /**
     * Get the errors generated by libxml_get_errors.
     *
     * @return LibXmlErrorCollection A collection of LibXMLError objects.
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
