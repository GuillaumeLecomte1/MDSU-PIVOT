#!/bin/bash

# Maximum number of retries
MAX_RETRIES=5
# Initial delay in seconds
DELAY=30

attempt=1
while [ $attempt -le $MAX_RETRIES ]; do
    echo "Attempt $attempt of $MAX_RETRIES to login to Docker Hub..."
    
    # Try to login
    if docker login; then
        echo "Successfully logged in to Docker Hub!"
        exit 0
    fi
    
    echo "Login failed. Waiting ${DELAY} seconds before next attempt..."
    sleep $DELAY
    
    # Increase delay for next attempt (exponential backoff)
    DELAY=$((DELAY * 2))
    attempt=$((attempt + 1))
done

echo "Failed to login after $MAX_RETRIES attempts."
exit 1 