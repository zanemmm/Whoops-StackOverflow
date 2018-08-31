<?php
if (!empty($message)) {
    $cacheFile = __DIR__ . '/../caches/cache.json';
    $cache = [];
    if (file_exists($cacheFile)) {
        $cache = json_decode(file_get_contents($cacheFile), true);
    }
    if (isset($cache[$message])) {
        $questions = $cache[$message];
    } else {
        try {
            $client = new GuzzleHttp\Client();
            $json = $client->get(
                "https://api.stackexchange.com/2.2/search/advanced"
                . "?page=1&pagesize=5&tagged=php&order=desc&sort=relevance&q=$message&accepted=True&site=stackoverflow"
            )->getBody()->getContents();
            $questions = json_decode($json, true)['items'];
            // save cache
            $cache[$message] = $questions;
            file_put_contents($cacheFile, json_encode($cache));
            // questions empty
            if (empty($questions)) {
                $wrong = "0 results found containing '$message'";
            }
        } catch (Exception $e) {
            $wrong = "something wrong about this package &lt;zane/whoops-stackoverflow&gt;: {$e->getMessage()}";
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
