<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Kaisei+Opti&family=Shippori+Mincho&display=swap"
        rel="stylesheet">
    <!-- CDN読み込み -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <title>和服フリマ（仮）- 出品登録確認</title>
</head>
<body>
    {{-- ヘッダー --}}
    @include('header');

    <div>
        <div>
            {{-- タイトル --}}
            <h1>商品登録確認</h1>
            <p>検索</p>
            <form action="/fleamarket/search" method="GET">
                <input type="text" name="keyword">
                <input type="submit" value="🔍">
            </form>
        </div>
        {{-- お気に入り商品閲覧ページ --}}
        @if ( session('user') )
            <a href="{{asset("/fleamarket/favorite")}}">お気に入り商品</a>
        @endif
        {{-- 出品ボタン --}}
        <a href="{{asset("/fleamarket/exhibit/new")}}">出品</a>
    </div>

    {{-- 商品登録確認表示 --}}
    <div>
        <p>商品名:{{$item_infos["name"]}}</p>
        <p>商品画像: </p>
        @foreach ( $item_infos["image"] as $img )
            <img src="{{$img}}">
        @endforeach
        <p>カテゴリ:{{$item_infos["category"]}}</p>
        <p>値段:{{$item_infos["price"]}}</p>
        <p>発送元都道府県:{{$item_infos["pref"]}}</p>
        <p>素材:{{$item_infos["material"]}}</p>
        <p>色:{{$item_infos["color"]}}</p>
        <p>商品状態:{{$item_infos["status"]}}</p>
        <p>におい:{{$item_infos["smell"]}}</p>
        <p>身丈:{{$item_infos["size_height"]}}</p>
        <p>裄丈:{{$item_infos["size_length"]}}</p>
        <p>袖丈:{{$item_infos["size_sleeve"]}}</p>
        <p>袖幅:{{$item_infos["size_sleeves"]}}</p>
        <p>前幅:{{$item_infos["size_front"]}}</p>
        <p>後幅:{{$item_infos["size_back"]}}</p>
        <p>自由記入:{{$item_infos["detail"]}}</p>

        <form action="/fleamarket/exhibit/done" method="POST">
            @csrf
            <input type="hidden" name="name" value="{{ $item_infos["name"] }}">
            @foreach ( $item_infos["image"] as $img )
                <input type="hidden" name="image[]" value="{{ $img }}">
            @endforeach
            <input type="hidden" name="category" value="{{ $item_infos["category"] }}">
            <input type="hidden" name="price" value="{{ $item_infos["price"] }}">
            <input type="hidden" name="pref" value="{{ $item_infos["pref"] }}">
            <input type="hidden" name="material" value="{{ $item_infos["material"] }}">
            <input type="hidden" name="color" value="{{ $item_infos["color"] }}">
            <input type="hidden" name="status" value="{{ $item_infos["status"] }}">
            <input type="hidden" name="smell" value="{{ $item_infos["smell"] }}">
            <input type="hidden" name="size_height" value="{{ $item_infos["size_height"] }}">
            <input type="hidden" name="size_length" value="{{ $item_infos["size_length"] }}">
            <input type="hidden" name="size_sleeve" value="{{ $item_infos["size_sleeve"] }}">
            <input type="hidden" name="size_sleeves" value="{{ $item_infos["size_sleeves"] }}">
            <input type="hidden" name="size_front" value="{{ $item_infos["size_front"] }}">
            <input type="hidden" name="size_back" value="{{ $item_infos["size_back"] }}">
            <input type="hidden" name="detail" value="{{ $item_infos["detail"] }}">

            <button name="back" type="submit" value="true">戻る</button>
            <button name="regist" type="submit" value="true">登録</button>
        </form>
    </div>
</body>
</html>