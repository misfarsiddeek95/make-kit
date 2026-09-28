<!DOCTYPE html>
<html lang="he" dir="rtl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הדפסת כל הקופונים (פעילים)</title>
    <style>
      @page {
        size: A4 portrait;
        margin: 8mm 6mm;
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
        background: #f5f5f5;
        direction: rtl;
        color: #333;
        padding: 70px 15px 30px;
      }

      .print-controls {
        position: fixed;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 999;
        display: flex;
        align-items: center;
        gap: 15px;
        background: #ffffff;
        padding: 8px 22px;
        border-radius: 30px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        border: 1px solid #e0e0e0;
      }

      .print-btn {
        background: #1d87e4;
        color: #fff;
        border: none;
        padding: 10px 28px;
        font-size: 15px;
        font-weight: 700;
        border-radius: 20px;
        cursor: pointer;
        font-family: inherit;
        direction: rtl;
        transition: background 0.2s ease;
      }

      .print-btn:hover {
        background: #1565c0;
      }

      .coupon-count-badge {
        font-size: 14px;
        font-weight: 600;
        color: #555;
      }

      .coupon-pages-container {
        max-width: 210mm;
        margin: 0 auto;
      }

      .coupon-page {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        gap: 8mm;
        width: 100%;
        height: 275mm;
        max-height: 275mm;
        margin-bottom: 25px;
        box-sizing: border-box;
        page-break-after: always;
        break-after: page;
      }

      .coupon-page:last-child {
        page-break-after: auto;
        break-after: auto;
        margin-bottom: 0;
      }

      .coupon-card {
        background: #fff;
        border: 2px dashed #1d87e4;
        border-radius: 12px;
        padding: 16px 14px;
        text-align: center;
        direction: rtl;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box;
        height: 100%;
        overflow: hidden;
      }

      .coupon-logo img {
        max-height: 32px;
        max-width: 120px;
        height: auto;
      }

      .coupon-wish {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 4px 0;
      }

      .coupon-image {
        margin: 4px auto;
      }

      .coupon-image img {
        max-height: 60px;
        max-width: 100px;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        object-fit: cover;
      }

      .coupon-code-label {
        font-size: 11px;
        color: #888;
        margin-bottom: 2px;
      }

      .coupon-code {
        font-size: 26px;
        font-weight: 700;
        color: #1d87e4;
        letter-spacing: 2px;
        direction: ltr;
        margin: 2px 0 6px;
      }

      .coupon-details {
        font-size: 11px;
        color: #666;
        border-top: 1px solid #eee;
        padding-top: 6px;
        margin-top: 4px;
      }

      .coupon-details span {
        display: inline-block;
        margin: 2px 6px;
      }

      .coupon-for-section {
        font-size: 11px;
        border-top: 1px solid #eee;
        padding-top: 4px;
        margin-top: 4px;
        color: #444;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .no-coupons {
        text-align: center;
        padding: 80px 20px;
        background: #fff;
        border-radius: 12px;
        max-width: 500px;
        margin: 40px auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      }

      .no-coupons h3 {
        font-size: 20px;
        color: #444;
        margin-bottom: 10px;
      }

      .no-coupons p {
        font-size: 14px;
        color: #777;
      }

      @media print {
        body {
          background: #fff !important;
          padding: 0 !important;
          margin: 0 !important;
        }

        .print-controls {
          display: none !important;
        }

        .coupon-pages-container {
          width: 100% !important;
          max-width: none !important;
          margin: 0 !important;
          padding: 0 !important;
        }

        .coupon-page {
          width: 100% !important;
          height: 275mm !important;
          max-height: 275mm !important;
          margin: 0 !important;
          padding: 0 !important;
          gap: 6mm !important;
          page-break-after: always !important;
          break-after: page !important;
          grid-template-columns: 1fr 1fr !important;
          grid-template-rows: 1fr 1fr !important;
        }

        .coupon-page:last-child {
          page-break-after: auto !important;
          break-after: auto !important;
        }

        .coupon-card {
          border: 2px dashed #1d87e4 !important;
          box-shadow: none !important;
          page-break-inside: avoid !important;
          break-inside: avoid !important;
          height: 100% !important;
          box-sizing: border-box !important;
        }
      }
    </style>
  </head>
  <body>
    <?php if (!empty($coupons)) { ?>
      <div class="print-controls">
        <span class="coupon-count-badge">סה"כ קופונים פעילים: <?=count($coupons)?> (4 בעמוד)</span>
        <button class="print-btn" onclick="window.print();">הדפס עכשיו</button>
      </div>

      <div class="coupon-pages-container">
        <?php
          $coupon_pages = array_chunk($coupons, 4);
          foreach ($coupon_pages as $page) {
        ?>
        <div class="coupon-page">
          <?php foreach ($page as $coupon) { ?>
          <div class="coupon-card">
            <div class="coupon-card-top">
              <div class="coupon-logo">
                <img src="<?=base_url()?>assets/img/logoBottom.png" alt="Logo">
              </div>
              <div class="coupon-wish">!מזל טוב, קיבלת קופון</div>
            </div>

            <div class="coupon-card-middle">
              <?php if (!empty($coupon->photo_path)) { ?>
              <div class="coupon-image">
                <img src="<?=base_url()?>photos/coupons/<?=htmlspecialchars($coupon->photo_path)?>-org.<?=htmlspecialchars($coupon->extension)?>" alt="Coupon Image">
              </div>
              <?php } ?>
              <div class="coupon-code-label">קוד קופון</div>
              <div class="coupon-code"><?=htmlspecialchars($coupon->coupon_code)?></div>
            </div>

            <div class="coupon-card-bottom">
              <div class="coupon-details">
                <?php $coupon_type = $coupon->coupon_type == 1 ? '%' : '₪'; ?>
                <span>סכום: <?=htmlspecialchars($coupon->coupon_amount)?> <?=$coupon_type?></span>
                <span>מתאריך: <?=htmlspecialchars($coupon->valid_from)?></span>
                <span>עד תאריך: <?=htmlspecialchars($coupon->valid_to)?></span>
              </div>

              <?php if (!empty($coupon->for_label) && !empty($coupon->for_value)) { ?>
              <div class="coupon-for-section">
                <strong><?=htmlspecialchars($coupon->for_label)?></strong>
                <?=htmlspecialchars($coupon->for_value)?>
              </div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    <?php } else { ?>
      <div class="no-coupons">
        <h3>לא נמצאו קופונים פעילים להדפסה</h3>
        <p>לא נמצאו קופונים בסטטוס פעיל בהתאם למסננים שנבחרו.</p>
      </div>
    <?php } ?>
  </body>
</html>
