<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineSearch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>CineSearch</h1>
    <form method="GET" id="searchForm">
        <input type="text" name="movie" id="movieInput" placeholder="Discover movies..." value="<?php echo isset($_GET['movie']) ? htmlspecialchars($_GET['movie']) : ''; ?>" required autocomplete="off">
        <button type="submit" id="searchBtn">
            <span class="button-text">Search</span>
            <span class="spinner"></span>
        </button>
    </form>

    <?php
    if(isset($_GET['movie']) && trim($_GET['movie']) !== ''){
        $movie = urlencode(trim($_GET['movie']));
        $apikey = "d26ba1bfde0b40687d299654706a4c78";
        $url = "https://api.themoviedb.org/3/search/movie?api_key=$apikey&query=$movie";
        
        // Suppress warnings for file_get_contents in case of network issues
        $response = @file_get_contents($url);
        
        if($response !== false){
            $data = json_decode($response, true);
            if(isset($data['total_results']) && $data['total_results'] > 0){
                $movieData = $data['results'][0];
                $title = htmlspecialchars($movieData['title']);
                $date = isset($movieData['release_date']) ? htmlspecialchars($movieData['release_date']) : '';
                $overview = isset($movieData['overview']) ? htmlspecialchars($movieData['overview']) : 'No overview available.';
                $poster_path = isset($movieData['poster_path']) ? $movieData['poster_path'] : null;
                
                echo '<div class="movie-card">';
                if($poster_path) {
                    $poster_url = "https://image.tmdb.org/t/p/w500" . $poster_path;
                    echo "<img class='poster' src='" . $poster_url . "' alt='" . $title . " Poster'>";
                } else {
                    echo "<div class='poster' style='display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.05);color:#94a3b8;height:450px;border:1px solid rgba(255,255,255,0.1);'>No Poster Available</div>";
                }
                echo '<div class="movie-info">';
                echo "<h2 class='movie-title'>" . $title . "</h2>";
                if ($date) {
                    echo "<p class='movie-date'>" . date('F j, Y', strtotime($date)) . "</p>";
                }
                echo "<p class='movie-overview'>" . $overview . "</p>";
                echo '</div>';
                echo '</div>';
            } else{
                echo "<div class='not-found'>No movies found matching your search. Try another title!</div>";
            }
        } else {
            echo "<div class='error'>Error fetching results. Please check your connection and try again.</div>";
        }
    }
    ?>

    <script>
        // Interactive Elements
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const input = document.getElementById('movieInput');
            if (input.value.trim() === '') {
                e.preventDefault();
                input.focus();
                return;
            }
            
            const btn = document.getElementById('searchBtn');
            btn.classList.add('loading');
            
            // We do not prevent default here, allowing the form to submit
            // The loading animation will show while the browser navigates
        });
        
        // Auto focus the input if there are no search results or on initial load
        window.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('movieInput');
            if(!input.value) {
                input.focus();
            }
        });
    </script>
</body>
</html>