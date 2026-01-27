#!/bin/bash
# Quick Start Guide - Run this script to start the application

echo "======================================"
echo "FigureStore - Quick Start"
echo "======================================"
echo ""

# Check Node.js
echo "📦 Checking Node.js..."
if ! command -v node &> /dev/null; then
    echo "❌ Node.js not found. Please install Node.js from https://nodejs.org"
    exit 1
fi
echo "✅ Node.js version: $(node --version)"
echo ""

# Check npm
echo "📦 Checking npm..."
if ! command -v npm &> /dev/null; then
    echo "❌ npm not found. Please install npm"
    exit 1
fi
echo "✅ npm version: $(npm --version)"
echo ""

# Install dependencies
echo "📥 Installing dependencies..."
cd frontend
npm install
if [ $? -ne 0 ]; then
    echo "❌ Failed to install dependencies"
    exit 1
fi
echo "✅ Dependencies installed"
echo ""

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env file created. Please review and update if needed."
fi
echo ""

# Start the app
echo "🚀 Starting React application..."
echo "The app will open in your browser at http://localhost:3000"
echo ""
echo "Make sure:"
echo "  1. PHP backend is running"
echo "  2. MySQL database is running"
echo "  3. .env file has correct API URL"
echo ""

npm start
