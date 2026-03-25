import winston from 'winston';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Default: project root /logs — override with LOG_DIR env variable
const logDir = process.env.LOG_DIR || path.resolve(__dirname, '../../../../logs');

const { combine, timestamp, printf, colorize } = winston.format;

const logFormat = printf(({ level, message, timestamp }) => `${timestamp} [${level}] ${message}`);

const logger = winston.createLogger({
    level: process.env.LOG_LEVEL || 'info',
    format: combine(timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }), logFormat),
    transports: [
        new winston.transports.File({
            filename: path.join(logDir, 'socket-err.log'),
            level: 'error',
        }),
        new winston.transports.File({
            filename: path.join(logDir, 'socket.log'),
        }),
    ],
});

if (process.env.NODE_ENV !== 'production') {
    logger.add(new winston.transports.Console({
        format: combine(colorize(), timestamp({ format: 'HH:mm:ss' }), logFormat),
    }));
}

export default logger;
