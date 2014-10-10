win
===

Words in Numbers, Multi Source Analytics


# Tips & Tricks
## Ablauf
Man sollte versuchen Hypothesen vorher aufzustellen (und ggf. Parameter mit dem ersten 2/3 der Daten optimieren) und dann das letzte Drittel verwenden, um zu gucken, ob das denn tatsaechlich stimmt.

## Crawling
ich empfehle meinen Studis immer, dass sie sich mal scrapy angucken sollen. Wobei ich jetzt einen habe, der apache nutch ausprobiert hat. Das scheint weit grausamer im Einrichten zu sein, kann dann aber auch mehr  im Sinne von der Studi hat 3 Tage Vollzeit gebraucht und ich habe immer noch nicht verstanden, wo ich wie was einstelle :p

## Datenmagie
ipython + pandas fuer Datenmagie.

### Tips vom Hedge Fund Manager
- focus only on sentiment extremes (pos & neg), e.g. not +-0.2
- look at dramatic CHANGES in sentiment from one direction to other
- take into account the earlier move in a stock, e.g. a stock underperforms the S&P500 for X days with
mostly negative sentiment - then suddenly one day sentiment goes positive: what is the reaction then?
- for any comparison such as 4.3, make sure to adjust stock daily performance for the general market
performance. In most cases, those stocks just went up that day because the MARKET went up as a whole. You want
to try to focus on what might be driving a single individual stock. THe adjustment can be done in many ways, but
typically by adjFactor = S&P500 performance *beta of the individual stock.
- it seems the study focuses on perc. change on the same day that the news is released. Are we sure about the exact
point in time that the news was released, i.e. was it before, during or after the trading session? This will
make a huge difference of course.
- also look at performance NEXT day (see previous point - as it will depend hugely on exactly when the news
was released)
- if possible, use all S&P500 stocks and not just 12.
