# BookingProject

Project made with Symfony 4.3.8, includes: 

-   CRUD operations on User entity.
-   CRUD operations on Trip entity.
-   Login Entity created to log failed login attempts.
-   Auth with CSRF Token.
-   1 minute ban on 5 login attempts per user/per minute. NOTICE: Vulnerable to DoS attacks.
-   ManyToMany relantioship added to User <-> Trips (for booking functionality).
-   VacantPlaces field added to Trip entity, so User cannot book more places than existent.
-   API endpoints added for: retrieving trips, trips with filtering + sorting + price range, particular trip by slug

For testing:

1. Go to /user to create dummy users.
2. Go to /trip to create dummy trips.
3. Go to /loginattempts to see failed auth.
4. Go to /login to test the auth.
5. All /api/* endpoints can be tested in PostMan, except the booking functionality.
