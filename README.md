# wymiana-walut
Proszę napisać próbkę kodu zgodną z poniższymi wymaganiami biznesowymi oraz:

- Rowziązanie zamodelowane w konwencji DomainDrivenDesign

- PHP w wersji 8.*

- Framework agnostic

- Całość przetestowana testami jednostkowymi

Zadanie "Wymiana walut":

Założenia:
Istnieją następujące kursy wymiany walut:
- EUR -> GBP 1.5678
- GBP -> EUR 1.5432

Klientowi naliczana jest opłata w wysokości 1% od kwoty:
- Wypłacanej klientowi w przypadku sprzedaży
- Pobieranej od klienta w przypadku zakupu

Przypadki:

- Klient sprzedaje 100 EUR za GBP
- Klient kupuje 100 GBP za EUR
- Klient sprzedaje 100 GBP za EUR
- Klient kupuje 100 EUR za GBP