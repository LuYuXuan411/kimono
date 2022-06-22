<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>お気に入り商品</title>
</head>
<body>
    {{-- ヘッダー --}}
    {{-- @include(); --}}

    {{-- フリマヘッダー --}}
    <div>
        <div>
            <h1>お気に入り商品</h1>
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

    {{-- 商品一覧 --}}
    {{-- 絞り込み機能 --}}
    <div>
        {{-- 販売商品のみ表示 --}}
        <label for="only_on_sale">販売商品のみを表示</label>
        <input type="checkbox" id="only_on_sale">

        {{-- カテゴリによる絞り込み --}}
        <label for="category">カテゴリ:</label>
        <select name="category" id="category">
            <option value="" selected disabled>選択してください</option>
            @foreach ( $categories as $category )
                <option value="{{$category}}">{{$category}}</option>
            @endforeach
        </select>

        <p>ソート</p>
    </div>
    {{-- 表示件数 --}}
    <div>
        <p>○件中○件表示</p>
    </div>
    {{-- 商品カード --}}
    <div>
        <div id="message">
            @isset( $msg )
                {{ $msg }}
            @endisset
        </div>

        <div id="item_card_wrapper">
            @foreach ( $item_infos as $item_info )
                <div id="item_card_{{$item_info["id"]}}"
                    data-is-on-sale="{{$item_info['onsale']==2? 'sold':'sale'}}"
                    data-category="{{$item_info['category']}}"
                >
                    <a href="{{asset('fleamarket/item/' . $item_info['id'] )}}">
                        <div>
                            <img src="{{asset($item_info["image"][0]["path"])}}">
                            <p> 商品名: {{ $item_info["name"] }}</p>
                            <p> 値段: {{ $item_info["price"] }}</p>
                        </div>
                    </a>
                    {{-- お気に入りから削除ボタン --}}
                    <div id="favorite_btn_wrapper">
                        <button onclick="deleteFavorite({{$item_info['id']}})">お気に入りから削除</button>
                        {{-- メッセージ表示エリア --}}
                        <div id="favorite_messages"></div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
    <script>
        let last_selected_category = '';

        function deleteFavorite(item_id){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax("/fleamarket/favorite/delete",{
                type: 'post',
                data: {
                    'item_id' : item_id,
                },
                dataType: 'json',
                success:function(data){
                    $('#item_card_' + item_id).empty();
                    $('#message').empty();
                    $('#message').append('<p>お気に入りから削除しました</p>');
                },
                error:function(error){
                    $('#message').empty();
                    $('#message').append('<p>お気に入りから削除できませんでした</p>');
                }
            });
        }

        // 販売商品の絞り込み
        $('#only_on_sale').change(function() {
            if( $('#only_on_sale').prop('checked') ){
                // チェックされた場合
                
                $('[data-is-on-sale="sold"]').each(function(i, e){
                    let sold_id = $(e).attr('id');
                    $('#' + sold_id).css('display', 'none');
                });
            }else{
                // チェックが外れた場合
                $('[data-is-on-sale="sold"]').each(function(i, e){
                    let sold_id = $(e).attr('id');
                    $('#' + sold_id).css('display', 'block');
                });
            }
        });

        // カテゴリによる絞り込み
        $('#category').change(function(){
            let category = $('[name=category] option:selected').text();
            if( last_selected_category !== '' ){
                $('#item_card_wrapper > div[data-category!=' + last_selected_category + ']').css('display', 'block');
            }
            $('#item_card_wrapper > div[data-category!=' + category + ']').css('display', 'none');
            last_selected_category = category;
        });
    </script>
</body>
</html>