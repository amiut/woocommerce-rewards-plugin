<?php
$rates = \Dornaweb\CustomerRewards\Conversion_Helper::get_rates();
?>
<div>
    <h4><?php _e('Conversion Rates', 'dwebcr'); ?></h4>
    <div class="rates">
        <?php foreach ($rates as $symbol => $rate) : ?>
            <div style="display: flex; align-items:center; gap: 10px; margin-bottom: 10px;">
                <input placeholder="symbol" name="rates[currency][]" type="text" required value="<?php echo $symbol; ?>" />
                <input placeholder="rate" name="rates[rate][]" type="text" required value="<?php echo $rate < 1 ? sprintf("%f", $rate) : floatval($rate); ?>" />

                <button type="button" class="button secondary" onclick="this.parentNode.remove(); return false;">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" class="button-primary rate-add-btn" onclick="">+</button>

    <script>
        document.querySelector('.rate-add-btn').addEventListener('click', function(e) {
            e.preventDefault();

            var $rates = document.querySelector('.rates');

            if (!$rates) return;

            var $item = document.createElement('div');
            $item.style.display = 'flex';
            $item.style.alignItems = 'center';
            $item.style.marginBottom = '10px';
            $item.style.gap = '10px';
            $item.innerHTML += '<input placeholder="symbol" name="rates[currency][]" type="text" required value="" />';
            $item.innerHTML += '<input placeholder="rate" name="rates[rate][]" type="text" required value="" />';

            var deleteBtn = document.createElement('button');
            deleteBtn.innerHTML = '&times;';
            deleteBtn.type = "button";
            deleteBtn.className = 'button secondary';
            deleteBtn.onclick = function() {
                this.parentNode.remove();
                return false;
            }
            $item.append(deleteBtn);

            $rates.append($item);
        });
    </script>
</div>
