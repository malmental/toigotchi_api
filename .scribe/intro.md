# Introduction

AI-powered virtual pet REST API with Passport authentication, pet CRUD operations, actions system, and AI chat integration.

<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>

    # Toigotchi API

    AI-powered virtual pet (Tamagotchi) REST API built with Laravel 13.

    ## Features
    - **Authentication**: Passport-based token auth
    - **Pet CRUD**: Create, read, update, delete virtual pets
    - **Pet Actions**: Feed, play, sleep, clean, heal, talk
    - **AI Chat**: AI-powered conversations with memory
    - **Stat Decay**: Realistic simulation with species modifiers
    - **Mood Engine**: Dynamic mood based on pet stats

    ## Authentication
    All endpoints except `/register` and `/login` require a Bearer token. 
    Obtain a token by registering and logging in. 
    Then include it in the `Authorization` header as `Bearer {token}`.

