<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order list</title>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>


    <h3>Order List</h3>
    <hr>

    <?php foreach ($data as $id => $order):?>
            <p>order id: <span class="oid"><?= $id ?></span> </p>
            <p>order status: <span class="status"><?= $order['status'] ?></span></p>
            <button class="cancel">取消订单</button>
            <button class="submit">支付订单</button>
            <hr>
    <?php endforeach;?>

    <script>
        $('.cancel').click((e) => {
            let order_id = $(e.target).prev().prev().find('.oid').text()
            let data = {
                'id' : order_id
            }

            $.post('/api/cancel', data, (res) => {
                console.log(res)
                $(e.target).prev().find('.status').text(res.data.status)
            })
        })

        $('.submit').click((e) => {
            let order_id = $(e.target).prev().prev().prev().find('.oid').text()
            let data = {
                'id' : order_id
            }

            $.post('/api/success', data, (res) => {
                console.log(res)
                $(e.target).prev().prev().find('.status').text(res.data.status)
            })
        })
    </script>
</body>
</html>