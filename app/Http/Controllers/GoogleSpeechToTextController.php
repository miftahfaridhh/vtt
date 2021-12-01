<?php

namespace App\Http\Controllers;

ini_set("upload_max_filesize", "100M");
ini_set("post_max_size", "100M");
ini_set('memory_limit', "1024M");

use App\FileUtils;
use Google\Cloud\Core\ExponentialBackoff;
use Google\Cloud\Speech\Operation;
use Google\Cloud\Speech\V1p1beta1\LongRunningRecognizeResponse;
use Google\Cloud\Speech\V1p1beta1\RecognitionAudio;
use Google\Cloud\Speech\V1p1beta1\RecognitionConfig;
use Google\Cloud\Speech\V1p1beta1\SpeechClient;
use Google\Cloud\Speech\V1p1beta1\SpeechRecognitionAlternative;
use Google\Cloud\Speech\V1p1beta1\SpeechRecognitionResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


include 'rest7.php';

/**
 * Class GoogleSpeechToTextController
 *
 * Parses a sound file input and returns a page showing the recognized text results.
 *
 * @package App\Http\Controllers
 */
class GoogleSpeechToTextController extends Controller
{

    public function results(Request $request)
    {

        // init google speech client
        $speechClient = new SpeechClient([
            'projectId' => env('GOOGLE_APPLICATION_PROJECT_ID'),
            'languageCode' => 'id-ID',
        ]);

        $file = $request->file('file'); //<-- we need to pass contents of this file.

        $fileFormat = FileUtils::getMimeFileFormat($file->getMimeType());
        $fileContent = null;

        /*
         * Google cloud throws a fit if the file isn't flac or flac; sorry mp3.
         * https://cloud.google.com/speech-to-text/docs/best-practices
         *
         * If we didn't receive flac, convert to flac
         */
        if ($fileFormat != 'flac') {
            $fileContent = file_get_contents($file);
        } else {
            $fileContent = file_get_contents($file);
        }

        if ($fileContent) {

            // upload to gcs
            $disk = Storage::disk('gcs');

            $randId = md5(time() . rand()) . '.flac';

            $disk->put($randId, $fileContent);
            $gcsUrl = 'gs://' . env('GOOGLE_CLOUD_STORAGE_BUCKET') . '/' . $randId;

            // config for recognition
            $config = new RecognitionConfig();
            $config->setEnableAutomaticPunctuation(true)
                ->setUseEnhanced(true)
                ->setLanguageCode('id-ID')
                ->setAudioChannelCount(1);
                //->setEnableSeparateRecognitionPerChannel(true);
            // create recognition audio from google cloud storage url
            $recognitionAudio = new RecognitionAudio();
            $recognitionAudio->setUri($gcsUrl);

            // begin operation
            $transcriptionOperation = $speechClient
                ->longRunningRecognize($config, $recognitionAudio);

            // wait for operation to complete
            $backoff = new ExponentialBackoff(10);
            $backoff->execute(function () use ($transcriptionOperation) {
                $transcriptionOperation->reload();
                if (!$transcriptionOperation->isDone()) {
                    throw new \Exception('still working', 404);
                }
            });


            // show results.
            $results = $transcriptionOperation->isDone() && $transcriptionOperation->operationSucceeded() ?
                $transcriptionOperation->getResult()->getResults() : null;
            // dd($results);

            return view('results', compact('results'));
        } else {
            return view('results');
        }
    }

}