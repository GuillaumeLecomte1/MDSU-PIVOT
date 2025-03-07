# Build the Docker image
docker build -t marketplace-app --build-arg PORT=4002 .

Write-Host "Docker image built successfully!" -ForegroundColor Green
Write-Host "To run the container, use:" -ForegroundColor Yellow
Write-Host "docker run -p 4002:4002 -e APP_ENV=production -e APP_KEY=your-app-key marketplace-app" -ForegroundColor Cyan 