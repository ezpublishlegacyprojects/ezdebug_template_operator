<?php
$ezpodesc = array (
  'desc' => 'Contains information about a file.',
  'persistent' => true,
  'attributes' => 
  array (
    'contentobject_attribute_id' => 
    array (
      'type' => 'string',
      'static' => true,
      'desc' => 'The ID number of the content object attribute that the file belongs to.',
    ),
    'version' => 
    array (
      'type' => 'string',
      'static' => true,
      'desc' => 'The version number of the object that the file belongs to.',
    ),
    'filename' => 
    array (
      'type' => 'string',
      'static' => true,
      'desc' => 'The internal name of the file (generated by the system).',
    ),
    'original_filename' => 
    array (
      'type' => 'string',
      'static' => true,
      'desc' => 'The original name of the file.',
    ),
    'mime_type' => 
    array (
      'type' => 'string',
      'static' => true,
      'desc' => 'The MIME type of the file (for example &quot;audio/wav&quot;).',
    ),
    'download_count' => 
    array (
      'type' => 'string',
      'static' => true,
      'desc' => 'The number of times the file has been downloaded through the &quot;download&quot; view of the &quot;content&quot; module.',
    ),
    'filesize' => 
    array (
      'type' => 'integer',
      'static' => false,
      'desc' => 'The size of the file (number of bytes).',
    ),
    'filepath' => 
    array (
      'type' => 'string',
      'static' => false,
      'desc' => 'The path to the file (including the filename).',
    ),
    'mime_type_category' => 
    array (
      'type' => 'string',
      'static' => false,
      'desc' => 'The MIME type category (for example &quot;audio&quot;).',
    ),
    'mime_type_part' => 
    array (
      'type' => 'string',
      'static' => false,
      'desc' => 'The MIME type part (for example &quot;wav&quot;).',
    ),
  ),
);
?>