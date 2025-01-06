import React from 'react';

export default function StatisticBox({ number, text }) {
    return (
        <div className="flex flex-col items-center">
            <span className="text-4xl font-bold text-green-500">{number}+</span>
            <span className="text-sm text-gray-600">{text}</span>
        </div>
    );
} 