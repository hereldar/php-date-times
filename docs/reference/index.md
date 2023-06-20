Reference
=========

Dates and Times
---------------

- [`DateTime`](date-time) stores a date and time with a time-zone.
  It is useful if you want to perform precise date and time
  calculations taking into account the [`TimeZone`](time-zone), such
  as America/Mexico_City.

- [`LocalDateTime`](local-date-time) stores a date and time without a
  time-zone, such as 17:30 on 3 December 2007.

- [`LocalDate`](local-date) stores a date without a time, such as 3
  December 2007. It could be used to store a birthday.

- [`LocalTime`](local-time) stores a time without a date, such as
  17:30. It could be used to store an opening or closing time.

Time Zones
----------

- [`TimeZone`](time-zone) stores a time-zone identifier, such as
  America/Mexico_City. It can also store a time-zone abbreviation such
  as BST or a fixed offset such as -06:00.

- [`Offset`](offset) stores time offset from Greenwich/UTC, such as
  -06:00.

Durations
---------

- [`Period`](period) stores an amount of time in individual units,
  such as months or hours.
