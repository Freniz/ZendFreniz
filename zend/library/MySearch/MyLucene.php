<?php

require_once ('Zend/Search/Lucene.php');
require_once 'MySearch/Document.php';
class Search_MyLucene extends Zend_Search_Lucene {
	
	public function getDocument($id)
	{
		if ($id instanceof Zend_Search_Lucene_Search_QueryHit) {
			/* @var $id Zend_Search_Lucene_Search_QueryHit */
			$id = $id->id;
		}
	
		if ($id >= $this->_docCount) {
			require_once 'Zend/Search/Lucene/Exception.php';
			throw new Zend_Search_Lucene_Exception('Document id is out of the range.');
		}
	
		$segmentStartId = 0;
		foreach ($this->_segmentInfos as $segmentInfo) {
			if ($segmentStartId + $segmentInfo->count() > $id) {
				break;
			}
	
			$segmentStartId += $segmentInfo->count();
		}
	
		$fdxFile = $segmentInfo->openCompoundFile('.fdx');
		$fdxFile->seek(($id-$segmentStartId)*8, SEEK_CUR);
		$fieldValuesPosition = $fdxFile->readLong();
	
		$fdtFile = $segmentInfo->openCompoundFile('.fdt');
		$fdtFile->seek($fieldValuesPosition, SEEK_CUR);
		$fieldCount = $fdtFile->readVInt();
	
		$doc = new Search_Document();
		for ($count = 0; $count < $fieldCount; $count++) {
			$fieldNum = $fdtFile->readVInt();
			$bits = $fdtFile->readByte();
	
			$fieldInfo = $segmentInfo->getField($fieldNum);
	
			if (!($bits & 2)) { // Text data
				$field = new Zend_Search_Lucene_Field($fieldInfo->name,
						$fdtFile->readString(),
						'UTF-8',
						true,
						$fieldInfo->isIndexed,
						$bits & 1 );
			} else {            // Binary data
				$field = new Zend_Search_Lucene_Field($fieldInfo->name,
						$fdtFile->readBinary(),
						'',
						true,
						$fieldInfo->isIndexed,
						$bits & 1,
						true );
			}
	
			$doc->addField($field);
		}
	
		return $doc;
	}
	
}

?>