# The House

Simple Forum written in PHP.

## How and Why?

I thought of building an old-school themed website in August 2021 and I was committed on writing it in an old-school nearly-dead language like PHP, so I actually made it and it was really good. However, it lacked a lot of features at the time, such as replying to other comments and setting profile pictures and so on.

Recently, I came back to this Project and I found it all covered in dust, the code is so old and I couldn't even remember writing some parts of it, but guess what? It worked! I was quite astonished at first but PHP doesn't really change that much, which is what I like about these mature languages, as they're stable for a long time and they don't change colours quickly... reminds me of.. uhh.. Next.js...

*It really looks like [Hacker News](https://news.ycombinator.com/) though...*

## Features

Overall, it turned out really good but comes with some deficiencies (I'm not a PHP Developer). Here's what I added to the original version I built in 2021:
- Light/Dark theme.
- Reply support.
- ton of bugs (but definitely removed a lot too in the process).
- NSFW checking (shoutout to @andresribeiro for making [the API](https://github.com/andresribeiro/nsfwjs-docker/)).
- Redirects.
- Ability to create categories if you are an Admin.
- More interesting things.

Please do not look at the Code. It's the literal definition of Spaghetti Development, organising all of these functions will take a ton of time.  
Whatever, man! it works so it's fine.

By the way, I'd appreciate it if one of you could dockerise this repo.

## Setup

1. Clone the Repostitory:
```
git clone https://github.com/QurashiAkh/the-house.git
```
2. Get PHP.
3. Get a MySQL (or a MariaDB) Database.
4. Insert the:
   - Database Host
   - Database Username
   - Database Password
in `www/library/connect.php`.
5. Run the Initialisation Query (`thehousedatabase.sql`) in your Database.
6. Get Docker
7. Pull & Run [the NSFW API](https://hub.docker.com/r/andresribeiroo/nsfwjs) on port `3333`.
8. Get Apache or Nginx then host the `www/` directory, or if you're just trying it out, you can run this command to get a local development server:
```bash
sudo php -S localhost:80 # port 80 needs sudo, however, you can use another port if you like.
```
9. Sign up for an account, then go to the database and set yourself `admin`.
10. Explore!

## TODOs
*^ More like things I'll never do in the future but just write them down to journal my laziness and absolute deficiency of adding more features to projects I make.*

- Break everything down to components for cleaner code.
- Add Video Support & check for NSFW in them.
- Add Notifications (going to settings in `account.php` and enabling it to get Notifications from threads you've engaged in).
- Remove this bug where if you delete a parent comment all children comments don't show up anymore.
- Make it more responsive.
- Add an emoji picker.
- Show both recent threads and posts in `user.php`.
- Preview your new PFP in account.php before finally setting it.
- Add Chatrooms.
