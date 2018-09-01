<?php
if (!empty($message)) {
    $cacheFile = __DIR__ . '/../caches/cache.json';
    $cache = [];
    if (file_exists($cacheFile)) {
        $cache = json_decode(file_get_contents($cacheFile), true);
        // clean cache when it more than 20
        if (count($cache) > 20) {
            $cache = [];
        }
    }
    // if the cache exists, use it
    if (isset($cache[$message])) {
        $questions = $cache[$message];
        // questions empty
        if (empty($questions)) {
            $wrong = "0 results found containing '$message'";
        }
    } else {
        try {
            $urlMessage = urlencode($message);
            $url = 'https://api.stackexchange.com/2.2/search/advanced'
                . '?page=1&pagesize=5&tagged=php&order=desc&sort=relevance&q='
                . $urlMessage
                . '&accepted=True&site=stackoverflow';

            $client = new GuzzleHttp\Client();
            $response = $client->get($url);
            // Make sure API return status code with 200.
            if ($response->getStatusCode() !== 200) {
                throw new Exception(
                    "Fail to access StackExchange API! The response status code return {$response->getStatusCode()}."
                );
            }

            $apiContent = json_decode($response->getBody()->getContents(), true);
            // Make sure get the right content of API
            if (!is_array($apiContent) || !array_key_exists('items', $apiContent)) {
                throw new Exception("Wrong content return from StackExchange API!");
            }
            $questions = $apiContent['items'];

            // save cache
            $cache[$message] = $questions;
            file_put_contents($cacheFile, json_encode($cache));
            // questions empty
            if (empty($questions)) {
                $wrong = "0 results found containing '$message'";
            }
        } catch (Exception $e) {
            $wrong = "Something wrong when getting questions from StackOverflow: <br/><br/> {$e->getMessage()}";
        }
    }
    ?>
    <div class="details">
      <h2 class="details-heading">StackOverflow:</h2>
      <div class="data-table-container" id="data-tables">
        <div class="data-table">
            <?php if (!empty($questions)) { ?>
                <?php foreach ($questions as $question) { ?>
                    <p style="font-size: 20px">
                        <a style="color: #3399EE" href="<?php echo $question['link']; ?>" target="_blank">
                            <?php echo $question['title']; ?>
                        </a>
                    </p>
                <?php } ?>
            <?php } ?>
            <?php if (isset($wrong)) { ?>
                <h3 style="font-size: 26px;color: #666666"><?php echo $wrong; ?></h3>
            <?php } ?>
        </div>
      </div>
    </div>
<?php } ?>
