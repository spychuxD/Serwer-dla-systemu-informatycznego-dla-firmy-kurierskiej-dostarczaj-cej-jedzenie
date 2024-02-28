# W tej sekcji szczegółowo opisano proces implementacji serwera, uwzględniając wykorzystane technologie, 
frameworki oraz metody zapewnienia bezpieczeństwa i efektywności.
```
W tym systemie architektura klient-serwer jest wykorzystywana do efektywnej
komunikacji między front-endem (klientem) a back-endem (serwerem). Serwer pełni
kluczową rolę jako centralny punkt przetwarzania i zarządzania danymi. Odpowiada za
przetwarzanie żądań od klientów, zarządzanie bazą danych, wykonanie logiki biznesowej
i odsyłanie odpowiedzi. Ta architektura umożliwia scentralizowane zarządzanie danymi
i funkcjonalnościami, co ułatwia skalowanie i utrzymanie systemu.
JSON Web Tokens są używane do autoryzacji i zarządzania sesjami użytkowników
w systemie. Po pomyślnym zalogowaniu, serwer generuje token JWT, który jest
przesyłany do klienta. Ten token, zawierający zaszyfrowane informacje o użytkowniku,
jest używany do uwierzytelnienia i autoryzacji kolejnych żądań użytkownika do serwera.
```
## Poniżej pokrótce opisano każdy z kontrolerów w systemie:
### „FavoriteRestaurantsController”: 
```
Zarządza ulubionymi restauracjami użytkowników, umożliwiając dodawanie, usuwanie i wyświetlanie ulubionych restauracji.
```
### „LocationController”: 
```
Obsługuje operacje związane z lokalizacjami, takie jak dodawanie i wyszukiwanie lokalizacji.
```
### „DishController”: 
```
Zarządza daniami oferowanymi przez restauracje, w tym dodawaniem, edytowaniem i usuwaniem dań.
```
### „CourierController”: 
```
Odpowiada za funkcje związane z kurierami, w tym zarządzanie ich profilami i przypisywanie do zamówień.
```
### „UserController”: 
```
Zarządza kontami użytkowników, umożliwiając rejestrację, logowanie itp.
```
### „RestaurantController”: 
```
Odpowiada za zarządzanie informacjami o restauracjach, w tym dodawanie, aktualizowanie i wyświetlanie informacji o restauracji.
```
### „RestaurantRatingsController”: 
```
Zarządza ocenami restauracji, umożliwiając użytkownikom ocenianie i przeglądanie ocen.
```
### „PaymentController”: 
```
Przetwarza płatności, zarządza transakcjami, a także dobiera najbliższego kuriera. Algorytm najpierw pobiera listę dostępnych kurierów, którzy nie są aktualnie przypisani do żadnego zamówienia. Dla każdego kuriera, algorytm oblicza odległość od lokalizacji restauracji, korzystając z funkcji „calculateDistance”. Ta funkcja używa wzoru haversine do obliczenia przybliżonej odległości między dwoma punktami na sferze ziemi, wykorzystując szerokość i długość geograficzną. Algorytm iteruje przez listę kurierów, porównując obliczone odległości. Kurier, który znajduje się najbliżej lokalizacji restauracji, jest wybierany jako najbliższy. Jeśli znajdzie najbliższego kuriera, algorytm zwraca jego dane. W przeciwnym razie zwraca informację o braku dostępnych kurierów. Kluczowe w tym algorytmie jest efektywne wykorzystanie danych geolokalizacyjnych i zastosowanie precyzyjnych metod obliczeniowych do znajdowania najbliższego dostępnego kuriera. To podejście zapewnia optymalizację czasu dostawy i zwiększa efektywność operacyjną systemu dostaw minimalizując odległość, którą musi on pokonać do restauracji. Opis formuły Havesine: kąt środkowy θ będzie wyrażony jako
Wzór havesine, https://en.wikipedia.org/wiki/Haversine_formula, Haversine_formula.html
Ta formuła jest tylko przybliżeniem, gdy stosowana jest do Ziemi, która nie jest idealną sferą: promień Ziemi waha się od 6356,752 km na biegunach do 6378,137 km na równiku. Co ważniejsze, promień krzywizny linii północ-południe na powierzchni Ziemi jest o 1% większy na biegunach (≈6399,594 km) niż na równiku (≈6335,439 km) - dlatego formuła haversine'ów i prawo cosinusów nie mogą być gwarantowane poprawne z dokładnością lepszą niż 0,5%.
```
```
W funkcji „calculateDistance” zastosowano dwa główne wzory matematyczne. Przyjmuje ona cztery argumenty „lat1”, „lng1” (szerokość i długość geograficzna punktu 1) oraz „lat2”, „lng2” (szerokość i długość geograficzna punktu 2). Ustawia promień Ziemi na 6371 kilometrów, co jest przybliżeniem średniego promienia Ziemi. Konwertuje szerokości i długości geograficzne punktów z stopni na radiany, a następnie oblicza różnicę szerokości i długość geograficznych między punktami. Oblicza wartość zmiennej „a” formuły haversine'ów oraz oblicza wartość zmiennej „c”, która jest oparta na kącie środkowym między punktami. Oblicza odległość, mnożąc promień Ziemi przez wartość „c”.
```
### „OrderController”: 
```
Zarządza procesem zamawiania, od składania zamówień po ich realizację.
```
### „MessageController”: 
```
Odpowiada za zarządzanie komunikacją w systemie, umożliwiając wysyłanie, odbieranie i zarządzanie wiadomościami między użytkownikami oraz kurierami
```
