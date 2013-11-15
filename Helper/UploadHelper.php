<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\FileFieldBundle\Helper;

class UploadHelper
{
    protected $iconUri;

    public function __construct($iconUri)
    {
        $this->iconUri = $iconUri;
    }

    /**
     * Get the icon filename associated with a file mime type.
     *
     * @param string $mime File mime type.
     *
     * @return string
     */
    public function getFileIcon($mime)
    {
        return $this->fileMimeMap($mime);
    }

    public function getIconUri()
    {
        return $this->iconUri ? : '/bundles/filefield/img/icons/';
    }

    /**
     * Map a icon name to a mime type. Allow for generic icons for mime types.
     *
     * @param string $mime Mimetype of file.
     *
     * @return string
     */
    public function fileMimeMap($mime)
    {
        // Check for media type categories.
        foreach (array('image', 'text', 'video', 'audio') as $category) {
            if (strpos($mime, $category . '/') === 0) {
                return $category . '-x-generic.png';
            }
        }

        switch ($mime) {
            // Word document types.
            case 'application/msword':
            case 'application/vnd.ms-word.document.macroEnabled.12':
            case 'application/vnd.oasis.opendocument.text':
            case 'application/vnd.oasis.opendocument.text-template':
            case 'application/vnd.oasis.opendocument.text-master':
            case 'application/vnd.oasis.opendocument.text-web':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.stardivision.writer':
            case 'application/vnd.sun.xml.writer':
            case 'application/vnd.sun.xml.writer.template':
            case 'application/vnd.sun.xml.writer.global':
            case 'application/vnd.wordperfect':
            case 'application/x-abiword':
            case 'application/x-applix-word':
            case 'application/x-kword':
            case 'application/x-kword-crypt':
                return 'x-office-document.png';

            // Spreadsheet document types.
            case 'application/vnd.ms-excel':
            case 'application/vnd.ms-excel.sheet.macroEnabled.12':
            case 'application/vnd.oasis.opendocument.spreadsheet':
            case 'application/vnd.oasis.opendocument.spreadsheet-template':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.stardivision.calc':
            case 'application/vnd.sun.xml.calc':
            case 'application/vnd.sun.xml.calc.template':
            case 'application/vnd.lotus-1-2-3':
            case 'application/x-applix-spreadsheet':
            case 'application/x-gnumeric':
            case 'application/x-kspread':
            case 'application/x-kspread-crypt':
                return 'x-office-spreadsheet.png';

            // Presentation document types.
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
            case 'application/vnd.oasis.opendocument.presentation':
            case 'application/vnd.oasis.opendocument.presentation-template':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            case 'application/vnd.stardivision.impress':
            case 'application/vnd.sun.xml.impress':
            case 'application/vnd.sun.xml.impress.template':
            case 'application/x-kpresenter':
                return 'x-office-presentation.png';

            // Compressed archive types.
            case 'application/zip':
            case 'application/x-zip':
            case 'application/stuffit':
            case 'application/x-stuffit':
            case 'application/x-7z-compressed':
            case 'application/x-ace':
            case 'application/x-arj':
            case 'application/x-bzip':
            case 'application/x-bzip-compressed-tar':
            case 'application/x-compress':
            case 'application/x-compressed-tar':
            case 'application/x-cpio-compressed':
            case 'application/x-deb':
            case 'application/x-gzip':
            case 'application/x-java-archive':
            case 'application/x-lha':
            case 'application/x-lhz':
            case 'application/x-lzop':
            case 'application/x-rar':
            case 'application/x-rpm':
            case 'application/x-tzo':
            case 'application/x-tar':
            case 'application/x-tarz':
            case 'application/x-tgz':
                return 'package-x-generic.png';

            // Script file types.
            case 'application/ecmascript':
            case 'application/javascript':
            case 'application/mathematica':
            case 'application/vnd.mozilla.xul+xml':
            case 'application/x-asp':
            case 'application/x-awk':
            case 'application/x-cgi':
            case 'application/x-csh':
            case 'application/x-m4':
            case 'application/x-perl':
            case 'application/x-php':
            case 'application/x-ruby':
            case 'application/x-shellscript':
            case 'text/vnd.wap.wmlscript':
            case 'text/x-emacs-lisp':
            case 'text/x-haskell':
            case 'text/x-literate-haskell':
            case 'text/x-lua':
            case 'text/x-makefile':
            case 'text/x-matlab':
            case 'text/x-python':
            case 'text/x-sql':
            case 'text/x-tcl':
                return 'text-x-script.png';

            // HTML aliases.
            case 'application/xhtml+xml':
                return 'text-html.png';

            // Executable types.
            case 'application/x-macbinary':
            case 'application/x-ms-dos-executable':
            case 'application/x-pef-executable':
                return 'application-x-executable.png';

            default:
                return 'application-octet-stream.png';
        }
    }

    public function formatSize($size)
    {
        $divisor = $size >= 1000000 ? 1000000 : 1000;

        return round($size / $divisor, 1) . ($divisor === 1000 ? 'kB' : 'MB');
    }
}
