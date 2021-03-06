<?php
/**----------------------------------------------------------------------------------
* Microsoft Developer & Platform Evangelism
*
* Copyright (c) Microsoft Corporation. All rights reserved.
*
* THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, 
* EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES 
* OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
*----------------------------------------------------------------------------------
* The example companies, organizations, products, domain names,
* e-mail addresses, logos, people, places, and events depicted
* herein are fictitious.  No association with any real company,
* organization, product, domain name, email address, logo, person,
* places, or events is intended or should be inferred.
*----------------------------------------------------------------------------------
**/

/** -------------------------------------------------------------
# Azure Storage Blob Sample - Demonstrate how to use the Blob Storage service. 
# Blob storage stores unstructured data such as text, binary data, documents or media files. 
# Blobs can be accessed from anywhere in the world via HTTP or HTTPS. 
#
# Documentation References: 
#  - Associated Article - https://docs.microsoft.com/en-us/azure/storage/blobs/storage-quickstart-blobs-php 
#  - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/ 
#  - Getting Started with Blobs - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-blobs/
#  - Blob Service Concepts - http://msdn.microsoft.com/en-us/library/dd179376.aspx 
#  - Blob Service REST API - http://msdn.microsoft.com/en-us/library/dd135733.aspx 
#  - Blob Service PHP API - https://github.com/Azure/azure-storage-php
#  - Storage Emulator - http://azure.microsoft.com/en-us/documentation/articles/storage-use-emulator/ 
#
**/

require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=dicodingstorage1;AccountKey=McnzC6LJdokER0prQK/Ws/NVcqgYBtzT0Hx3mLHm4lz1GlFnrECQDFDAofMz+Y9lvqszwZzbtPmGWvoIr7h6QQ==";

// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

// Create container options object.
$createContainerOptions = new CreateContainerOptions();

// Set public access policy. Possible values are
// PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
// CONTAINER_AND_BLOBS:
// Specifies full public read access for container and blob data.
// proxys can enumerate blobs within the container via anonymous
// request, but cannot enumerate containers within the storage account.
//
// BLOBS_ONLY:
// Specifies public read access for blobs. Blob data within this
// container can be read via anonymous request, but container data is not
// available. proxys cannot enumerate blobs within the container via
// anonymous request.
// If this value is not specified in the request, container data is
// private to the account owner.
$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

// Set container metadata.
$createContainerOptions->addMetaData("key1", "gambar");
$createContainerOptions->addMetaData("key2", "analisa");

$containerName = "blockblobs1321";

try {
	// Create container.
	$blobClient->createContainer($containerName, $createContainerOptions);

}
catch(ServiceException $e){

	//$code = $e->getCode();
	//$error_message = $e->getMessage();
	//echo $code.": ".$error_message."<br />";
}

if(isset($_FILES['image'])){

		$fileToUpload = $_FILES['image']['tmp_name'];

		try{	

			
			$fileName = $_FILES['image']['name'];
			
			//$myfile = fopen($fileToUpload, "w") or die("Unable to open file!");
			//fclose($myfile);
			
			# Upload file as a block blob
			echo "Uploading BlockBlob: ".PHP_EOL;
			echo $fileName;
			echo "<br />";
			
			$content = file_get_contents($fileToUpload);

			//Upload blob
			$blobClient->createBlockBlob($containerName, $fileName, $content);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179439.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}
		catch(InvalidArgumentTypeException $e){
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}
}	else {
	if (isset($_GET["Cleanup"])) { 

		try{
			// Delete container.
			echo "Deleting Container".PHP_EOL;
			echo $_GET["containerName"].PHP_EOL;
			echo "<br />";
			$blobClient->deleteContainer($_GET["containerName"]);
		} catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179439.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}
	}
}	
?>
<html>
<head>
  <title>Upload Gambar</title>
</head> 
<body>
<table>
<tr><td>Upload Gambar</td></tr>	
<tr>
<td>
<form method="post" action="index.php"  enctype="multipart/form-data">
    <input type="file" name="image" />
    <button type="submit">Kirim</button>
</form>
</td>
</tr>
<tr>	
<?php
try {
	$listBlobsOptions = new ListBlobsOptions();
	//$listBlobsOptions->setPrefix("Upload");

	do {
?>
<td>
<?php	
		$i = 1;
		$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
		foreach ($result->getBlobs() as $blob) 	{
?>
		<b><a href="analisa.php?url=<?php echo $blob->getUrl() ?>"><?php echo $blob->getName(); ?></a></b><br />
		<img width="120" src="<?php echo $blob->getUrl() ?>"/>
		<hr/>
<?php	
			
					//echo $blob->getName().": ".$blob->getUrl()."<br />";
		}
			
		$listBlobsOptions->setContinuationToken($result->getContinuationToken());
?>	
</td>
<?php	
	} while($result->getContinuationToken());

} catch(ServiceException $e){
?>
<td>
<?php 	
	$code = $e->getCode();
	$error_message = $e->getMessage();
	echo $code.": ".$error_message."<br />";
?>
</td>	
<?php	
}
?>
<!-- </tr>
<tr><td><form>Isi nama file dari daftar gambar yg sudah di upload di atas untuk dianalisa:<br/> <input type="text" /><button type="button">Analisa</button></form>
<tr><td><h3>Hasil Analisa</h3></td></tr>
<tr><td><img src="" id="hasil" /></td></td>
<tr><td>Captions</td><tr>
<tr><td><span id="captions">&nbsp;</span></td></tr>
<tr><td>Info Lengkap</td><tr>
<tr><td><span id="info">&nbsp;</span></td></tr> -->
<tr><td><a href="index.php?Cleanup=1&containerName=<?php echo $containerName; ?>">Hapus storage</a></td></tr>
</table>
</body>
</html>
