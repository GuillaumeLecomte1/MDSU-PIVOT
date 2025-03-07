#!/bin/bash

# Build the Docker image
docker build -t marketplace-app --build-arg PORT=4002 .

echo "Docker image built successfully!"
echo "To run the container, use:"
echo "docker run -p 4002:4002 -e APP_ENV=production -e APP_KEY=your-app-key marketplace-app" 