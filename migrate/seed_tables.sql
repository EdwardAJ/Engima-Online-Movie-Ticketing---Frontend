INSERT INTO `movies`
    (`title`, `description`, `duration`, `movie_picture_url`, `release_date`)
VALUES
    ('Captain America: The First Avenger', "It\'s America\'s Ass.", '124', 'http://localhost/img/movies/america.jpg', '2011-07-11'),
    ('Captain America: Civil War', "Political involvement in the Avengers' affairs causes a rift between Captain America and Iron Man.", '148', 'http://localhost/img/movies/civilwar.jpg', '2016-05-06'),
    ('Captain America: The Winter Soldier', "As Steve Rogers struggles to embrace his role in the modern world, he teams up with a fellow Avenger and S.H.I.E.L.D agent, Black Widow, to battle a new threat from history: an assassin known as the Winter Soldier.", '136', 'http://localhost/img/movies/wintersoldier.jpg', '2014-04-04'),
    ('Avengers: Endgame', "After the devastating events of Avengers: Infinity War (2018), the universe is in ruins. With the help of remaining allies, the Avengers assemble once more in order to reverse Thanos' actions and restore balance to the universe.", '181', 'http://localhost/img/movies/endgame.jpg', '2019-04-26'),
    ('Captain Marvel', "Carol Danvers becomes one of the universe's most powerful heroes when Earth is caught in the middle of a galactic war between two alien races.", '123', 'http://localhost/img/movies/marvel.jpg', '2018-03-08'),
    ('Naruto Shippuden: The Movie 1', "Naruto Shippūden the Movie (劇場版 NARUTO -ナルト- 疾風伝, Gekijōban Naruto Shippūden) is a 2007 film directed by Hajime Kamegaki and written by Junki Takegami.", '95', 'http://localhost/img/movies/naruto.jpg', '2007-08-04'),
    ('Captain Phillips', "The true story of Captain Richard Phillips and the 2009 hijacking by Somali pirates of the U.S.-flagged MV Maersk Alabama, the first American cargo ship to be hijacked in two hundred years.", '134', 'http://localhost/img/movies/phillips.jpg', '2013-10-11'),
    ('Gundala', "Indonesia's preeminent comic book superhero and his alter ego Sancaka enter the cinematic universe to battle the wicked Pengkor and his diabolical squad of orphan assassins.", '123', 'http://localhost/img/movies/gundala.jpg', '2019-08-29'),
    ('Avatar: The Last Airbender', "We do not talk about this movie.", '103', 'http://localhost/img/movies/airbender.png', '2010-07-01'),
    ('Suster Ngesot', "Vira (18) pretty girl. She is a nurse and have friend named Silla (18) pretty girl, a nurse, sexy. They go from bandung to Jakarta to work as nurses, a job that they got.", '87', 'http://localhost/img/movies/ngesot.jpg', '2007-05-10');

INSERT INTO `genres`
    (`name`)
VALUES
    ('Action'),
    ('Adventure'),
    ('Sci-Fi'),
    ('Animation'),
    ('Biography'),
    ('Drama'),
    ('Thriller'),
    ('Crime'),
    ('Family'),
    ('Horror');

INSERT INTO `movie_genres`
    (`movie_id`, `genre_id`)
VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (2, 1),
    (2, 2),
    (2, 3),
    (3, 1),
    (3, 2),
    (3, 3),
    (4, 1),
    (4, 2),
    (4, 3),
    (5, 1),
    (5, 2),
    (5, 3),
    (6, 1),
    (6, 2),
    (6, 4),
    (7, 5),
    (7, 6),
    (7, 7),
    (8, 1),
    (8, 6),
    (8, 8),
    (9, 1),
    (9, 2),
    (9, 9),
    (10, 10),
    (10, 7);

INSERT INTO `screenings`
    (`movie_id`, `show_time`, `price`, `seats`)
VALUES
    (1, '2019-04-01 08:00:00', 60000, 0),
    (1, '2019-11-03 13:00:00', 60000, 0),
    (1, '2019-11-05 17:00:00', 60000, 0),
    (2, '2019-11-01 08:00:00', 60000, 0),
    (2, '2019-11-03 13:00:00', 60000, 0),
    (2, '2019-11-05 17:00:00', 60000, 0),
    (3, '2019-04-01 08:00:00', 60000, 0),
    (3, '2019-11-03 13:00:00', 60000, 0),
    (3, '2019-11-05 17:00:00', 60000, 0),
    (4, '2019-11-01 08:00:00', 60000, 0),
    (4, '2019-11-03 13:00:00', 60000, 0),
    (4, '2019-11-05 17:00:00', 60000, 0),
    (5, '2019-04-01 08:00:00', 60000, 0),
    (5, '2019-11-03 13:00:00', 60000, 0),
    (5, '2019-11-05 17:00:00', 60000, 0),
    (6, '2019-11-02 08:00:00', 30000, 0),
    (6, '2019-11-04 13:00:00', 30000, 0),
    (7, '2019-04-01 08:00:00', 45000, 0),
    (7, '2019-11-01 13:00:00', 45000, 0),
    (8, '2019-11-01 08:00:00', 100000, 0),
    (8, '2019-12-01 13:00:00', 100000, 0),
    (9, '2019-04-01 08:00:00', 10000, 0),
    (9, '2019-04-01 13:00:00', 10000, 0);