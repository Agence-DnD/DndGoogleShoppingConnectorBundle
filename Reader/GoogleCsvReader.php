<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Reader;

use Symfony\Component\Validator\Constraints as Assert;
use Pim\Component\Connector\Reader\File\CsvReader;

/**
 * Google Csv reader
 *
 * @author    Florian Fauvel <florian.fauvel@dnd.fr>
 * @copyright 2016 Agence Dn'D (http://www.dnd.fr)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GoogleCsvReader extends CsvReader{

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (null === $this->csv) {
            $this->initializeRead();
        }

        $data = $this->csv->fgetcsv();

        if (false !== $data) {
            if ([null] === $data || null === $data) {
                return null;
            }
            if ($this->stepExecution) {
                $this->stepExecution->incrementSummaryInfo('read_lines');
            }
        } elseif ($this->csv->eof()) {
            $data = null;
        } else {
            throw new \RuntimeException('An error occurred while reading the csv.');
        }

        return $data;
    }

    /**
     * Initialize read process by extracting zip if needed, setting CSV options
     * and settings field names.
     */
    protected function initializeRead()
    {
        // TODO mime_content_type is deprecated, use Symfony\Component\HttpFoundation\File\MimeTypeMimeTypeGuesser?
        if ('application/zip' === mime_content_type($this->filePath)) {
            $this->extractZipArchive();
        }

        $this->csv = new \SplFileObject($this->filePath);
        $this->csv->setFlags(
            \SplFileObject::READ_CSV   |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::SKIP_EMPTY
        );
        $this->csv->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
    }

}
